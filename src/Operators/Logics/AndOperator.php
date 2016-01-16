<?php namespace Calculator\Operators\Logics;


use Calculator\Operators\BaseOperator;

class AndOperator extends BaseOperator {

    protected $symbol = 'AND';
    protected $priority = 2;

    public function process($index, \SplDoublyLinkedList $list)
    {
        $first = $list->offsetGet($index - 1);
        $second = $list->offsetGet($index + 1);
        $result = $first && $second;
        $list->offsetSet($index - 1, $result);
        unset($list[ $index ]);
        unset($list[ $index ]);
    }
}