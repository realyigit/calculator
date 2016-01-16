<?php
/**
 * Created by PhpStorm.
 * User: yigit
 * Date: 16.01.2016
 * Time: 02:03
 */

namespace Calculator\Operators\Logics;


use Calculator\Operators\BaseOperator;

class OrOperator extends BaseOperator {
    protected $symbol = 'OR';
    protected $priority = 2;

    public function process($index, \SplDoublyLinkedList $list)
    {
        $first = $list->offsetGet($index - 1);
        $second = $list->offsetGet($index + 1);
        $result = (bool)$first || (bool)$second;
        $list->offsetSet($index - 1, $result);
        unset($list[ $index ]);
        unset($list[ $index ]);
    }
}