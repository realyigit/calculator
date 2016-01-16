<?php namespace Calculator;

use Calculator\Operators\Arithmetics\Addition;
use Calculator\Operators\Arithmetics\Division;
use Calculator\Operators\Arithmetics\Multiplication;
use Calculator\Operators\Arithmetics\Subtraction;
use Calculator\Operators\BaseOperator;
use Calculator\Operators\Logics\AndOperator;
use Calculator\Operators\Logics\OrOperator;
use Calculator\Operators\Logics\XnorOperator;
use Calculator\Operators\Logics\XorOperator;

class Calculator {

    protected $formula;
    protected $originFormula;
    public static $operandsClasses = [];
    public static $symbols = [];

    protected $brackets = '()';
    protected $replacementSymbols = ['[' => '(', '{' => '(', ']' => ')', '}' => ')'];

    public function __construct($formula = null)
    {
        //default extended operators
        //Arithmetic
        static::extend(Addition::class);
        static::extend(Subtraction::class);
        static::extend(Multiplication::class);
        static::extend(Division::class);

        //Logic
        static::extend(AndOperator::class);
        static::extend(OrOperator::class);
        static::extend(XorOperator::class);
        static::extend(XnorOperator::class);

        if ($formula) {
            $this->setFormula($formula);
        }
    }

    public function setFormula($formula)
    {
        $this->formula = $formula;
        $this->originFormula = $formula;

        return $this;
    }

    public static function extend($classNames)
    {
        if (is_array($classNames)) {
            foreach ($classNames as $className) {
                static::extend($className);
            }
        }
        $class = new $classNames;
        if (!$class instanceof BaseOperator) {
            throw new \Exception($classNames . ' is not implements from BaseOperator!');
        }
        static::$symbols[ $class->getSymbol() ] = $class->getPriority();
        static::$operandsClasses[ $class->getSymbol() ] = $class;
    }

    public function result($variables = null)
    {
        $this->replace($variables);

        return $this->solve($this->formula);
    }

    private function replace($variables)
    {
        $this->formula = str_replace(array_keys($this->replacementSymbols), array_values($this->replacementSymbols), $this->formula);
        if ($variables) $this->formula = str_replace(array_keys($variables), array_values($variables), $this->formula);
    }

    private function solve($formula)
    {
        $findSubFormula = false;
        $stringStart = 0;
        $stringLength = 0;
        $stack = new \SplStack();
        $subFormulas = [];
        for ($i = 0; $i < strlen($formula); $i++) {
            $char = $formula[ $i ];
            if ($this->isBracketOpen($char)) {
                if ($findSubFormula == false && $stack->count() == 0) {
                    $stringStart = $i;
                }
                $stack->push(1);
                $findSubFormula = true;
            }
            if ($findSubFormula) $stringLength++;
            if ($this->isBracketClose($char)) {
                $stack->pop();
                if ($stack->count() === 0 && $findSubFormula) {
                    $subFormulas[ substr($formula, $stringStart, $stringLength) ] = substr($formula, $stringStart, $stringLength);
                    $findSubFormula = false;
                    $stringLength = 0;
                }
            }
        }
        if (count($subFormulas) > 0) {
            foreach ($subFormulas as &$subFormula) {
                $temp = trim(substr($subFormula, 1, strlen($subFormula) - 2));
                $subFormula = $this->solve($temp);
            }
            $formula = str_replace(array_keys($subFormulas), array_values($subFormulas), $formula);
        }
        $elems = new \SplDoublyLinkedList;
        array_map(function ($item) use ($elems) {
            if ($item != ' ') $elems->push($item);
        }, explode(' ', $formula));

        while ($elems->count() > 1) {
            $maxPriority = 0;
            $index = 0;
            foreach ($elems as $i => $el) {
                if (isset(static::$symbols[ $el ]) && static::$symbols[ $el ] > $maxPriority) {
                    $maxPriority = static::$symbols[ $el ];
                    $index = $i;
                }
            }

            $this->process($index, $elems);
        };

        return $elems->pop();
    }

    private function isBracketOpen($i)
    {
        return $i == $this->brackets[0];
    }

    private function isBracketClose($i)
    {
        return $i == $this->brackets[1];
    }

    private function process($index, \SplDoublyLinkedList $list)
    {
        $operator = $list->offsetGet($index);

        if (!isset(static::$operandsClasses[ $operator ])) throw new \Exception ('Unknown operator:'.$operator);

        return static::$operandsClasses[ $operator ]->process($index, $list);
    }
}