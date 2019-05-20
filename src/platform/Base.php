<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nbcx\pay\platform;

use nbcx\pay\Component;

/**
 * Base
 *
 * @package connector
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2019/5/9
 */
abstract class Base extends Component {

    /**
     * @var Component
     */
    protected $pay;

    final public function __construct(Component $pay) {
        $this->pay = $pay;
    }

    public function config(array $config) {
        $this->pay->config($config);
    }


    public function __get($name) {
        // TODO: Implement __get() method.
        return isset($this->pay->config[$name])?$this->pay->config[$name]:null;
    }

    public function __set($name, $value) {
        // TODO: Implement __set() method.
        $this->pay->config[$name] = $value;
    }

}