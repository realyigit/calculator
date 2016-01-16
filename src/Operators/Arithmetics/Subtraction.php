<?php namespace Calculator\Operators\Arithmetics;

use Calculator\Operators\BaseOperator;

class Subtraction extends BaseOperator {
    protected $symbol = '-';
    protected $priority = 3;

    public function process($index, \SplDoublyLinkedList $list)
    {
        $first = $list->offsetGet($index - 1);
        $second = $list->offsetGet($index + 1);
        $result = $first - $second;
        $list->offsetSet($index - 1, $result);
        unset($list[ $index ]);
        unset($list[ $index ]);
    }
}