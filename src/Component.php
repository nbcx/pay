<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nbcx\pay;

/**
 * IPay
 *
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2019/4/25
 */
abstract class Component {

    public $errno;
    public $errmsg;

    protected $config = [];

    public function config(array $config) {
        $this->config = array_merge($this->config,$config);
    }

    abstract public function get();
}