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

    //支付平台
    public function setPlatform() {

    }

    public function set($config) {
        $this->config = $config;
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