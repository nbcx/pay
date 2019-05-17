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

use nbcx\pay\platform\Weixin;

/**
 * Pay
 *
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2019/4/26
 */
class Pay {

    public $errno;
    public $errmsg;

    protected $config;

    protected $platform;

    protected $input = [];

    protected $name;

    public function __construct($name,$type=null,$config=[]) {
        $this->name = $name;
        $this->config($config);
        $type and $this->type($type);
    }

    public function config($config) {
        $this->config = $config;
    }

    public function type($name) {
        // TODO: Implement setType() method.
        if(strstr($name,"\\")) {
            $connector = new $name();
        }
        else {
            $type = ucfirst($name);
            $connector = "nbcx\\pay\\platform\\{$this->name}\\$type";

            $connector = new $connector();
        }
        $this->connector = $connector;
        return $this;
    }

    public function unifiedOrder($platform,$type,$param) {

        $ipay = new Weixin($this->config);
        $result = $ipay->unifiedOrder($type,$param);

        if($result) {
            return $result;
        }

        $this->errno = $this->errno;
        $this->errmsg = $ipay->errmsg;

        return false;
    }



}