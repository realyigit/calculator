<?php namespace Calculator\Operators;


abstract class BaseOperator {

    protected $symbol = '';
    protected $priority = 1;

    abstract public function process($index, \SplDoublyLinkedList $list);

    /**
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }
}