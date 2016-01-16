<?php

require_once 'vendor/autoload.php';
class BiggerOperator extends \Calculator\Operators\BaseOperator{
    protected $symbol = '>';
    protected $priority = 2;
    public function process($index, \SplDoublyLinkedList $list)
    {
        $first = $list->offsetGet($index - 1);
        $second = $list->offsetGet($index + 1);
        $result = (bool)($first > $second);
        $list->offsetSet($index - 1, $result);
        unset($list[ $index ]);
        unset($list[ $index ]);
    }
}

$formula = '( ( 10 * x + y ) / z AND ( 20 OR 0 ) XOR ( x XNOR y )  ) OR 1 + 2';
$calculator = new \Calculator\Calculator($formula);
//$calculator->setFormula($formula);
\Calculator\Calculator::extend(BiggerOperator::class);
var_dump($calculator->result(['x' => 10, 'y' => 20, 'z' => 2]));