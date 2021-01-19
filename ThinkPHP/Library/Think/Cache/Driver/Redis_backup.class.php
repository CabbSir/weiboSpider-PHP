<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think\Cache\Driver;

use Think\Cache;

defined('THINK_PATH') or exit();

/**
 * Redis缓存驱动
 * 要求安装phpredis扩展：https://github.com/nicolasff/phpredis
 */
class Redis extends Cache
{
    /**
     * Redis constructor.
     * @param array $options
     * @throws \Think\Exception
     */
    public function __construct($options = [])
    {
        if (!extension_loaded('redis')) {
            E(L('_NOT_SUPPORT_') . ':redis');
        }

        $options = array_merge([
            'host'       => C('REDIS_HOST') ?: '127.0.0.1',
            'port'       => C('REDIS_PORT') ?: 6379,
            'db'         => C('DATA_CACHE_DB') ?: 0,
            'timeout'    => C('DATA_CACHE_TIMEOUT') ?: false,
            'persistent' => false,
            'expire'     => C('DATA_CACHE_TIME') ?: 0,
            'prefix'     => C('DATA_CACHE_PREFIX') ?: '',
            'length'     => 0
        ], $options ?: []);

        $this->options = $options;
        $func          = $options['persistent'] ? 'pconnect' : 'connect';
        $this->handler = new \Redis;
        $options['timeout'] === false ?
            $this->handler->$func($options['host'], $options['port']) :
            $this->handler->$func($options['host'], $options['port'], $options['timeout']);

        $options['db'] && $this->handler->select($options['db']);
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name)
    {
        //tiancheng
        //N('cache_read',1);
        $value    = $this->handler->get($this->options['prefix'] . $name);
        $jsonData = json_decode($value, true);

        return ($jsonData === null) ? $value : $jsonData;    //检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param integer $expire 有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        //tiancheng
        //N('cache_write',1);
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }

        $name = $this->options['prefix'] . $name;
        //对数组/对象数据进行缓存处理，保证数据完整性
        $value = (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        if (is_int($expire) && 0 !== $expire) {
            $result = $this->handler->setex($name, $expire, $value);
        } else {
            $result = $this->handler->set($name, $value);
        }
        if ($result && $this->options['length'] > 0) {
            // 记录缓存队列
            $this->queue($name);
        }

        return $result;
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name)
    {
        $key = [];
        if (is_array($name)) {
            foreach ($name as $v) {
                $key[] = $this->options['prefix'] . $v;
            }
        } else {
            $key = $this->options['prefix'] . $name;
        }

        return $this->handler->del($key);
    }

    /**
     * KEY值自增
     * @access public
     * @param String $name 缓存变量名
     * @param int $step 增加值
     * @return int
     */
    public function incre($name, $step = 1)
    {
        return $this->handler->incrBy($this->options['prefix'] . $name, $step);
    }

    /**
     * KEY值自减
     * @access public
     * @param string $name 缓存变量名
     * @param int $step 减少值
     * @return int
     */
    public function decre($name, $step = 1)
    {
        return $this->handler->decrBy($this->options['prefix'] . $name, $step);
    }

    /**
     * 设定KEY过期时间
     * @param $name
     * @param $expire
     * @return bool
     */
    public function expire($name, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }

        return $this->handler->expire($this->options['prefix'] . $name, $expire);
    }

    /**
     * 按照规则批量获取KEY
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function keys($name)
    {
        return $this->handler->keys($this->options['prefix'] . $name);
    }

    /**
     * 调用redis其他方法
     * @param $method
     * @param $args
     * @return mixed
     * @throws \Think\Exception
     */
    public function __call($method, $args)
    {
        //给key添加前缀
        $no_prefix_config = ['select', 'eval'];
        if (count($args) >= 1 && !in_array($method, $no_prefix_config)) {
            $args[0] = $this->options['prefix'] . $args[0];
        }
        //调用缓存类型自己的方法
        if (method_exists($this->handler, $method)) {
            return call_user_func_array([$this->handler, $method], $args);
        } else {
            E(__CLASS__ . ':' . $method . L('_METHOD_NOT_EXIST_'));

            return false;
        }
    }

    public function setExpire($name, $ttl)
    {
        return $this->handler->EXPIRE($this->options['prefix'] . $name, $ttl);
    }
}
