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

use nbcx\pay\platform\Base;

/**
 * Pay
 *
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2019/4/26
 */
class Pay extends Component {

    /**
     * @var Base
     */
    protected $platform;

    protected $name;

    public $config = [
        'fee_type' => 'CNY'
    ];

    public function __construct($name,array $config=[]) {
        $this->name = $name;
        $this->config($config);
    }

    public function type($name) {
        if(strstr($name,"\\")) {
            $connector = new $name();
        }
        else {
            $type = ucfirst($name);
            $connector = "nbcx\\pay\\platform\\{$this->name}\\$type";

            $connector = new $connector($this);
        }
        $this->platform = $connector;
        return $this;
    }

    public function unifiedOrder(array $param) {
        $this->type('unifiedOrder');
        $this->config($param);
        return $this->get();
    }

    public function query($order_id) {
        $this->type('query');
        $this->config([
            'order_id' => $order_id
        ]);
        return $this->get();
    }

    public function get() {
        if($this->platform == null) {
            throw new \Error('platform is not exits');
        }
        $result =  $this->platform->get();

        if($result) {
            return $result;
        }
        return false;
    }

}