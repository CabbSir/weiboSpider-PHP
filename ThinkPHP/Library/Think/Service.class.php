<?php
namespace Think;

class Service
{
    private static $_instances;

    protected $_modelName = '\Think\Model';

    /**
     * @var Model
     */
    protected $_model;

    /**
     * @return $this
     */
    public static function getInstance()
    {
        $class = get_called_class();

        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new $class();
        }

        return self::$_instances[$class];
    }

    public function __construct()
    {
        $this->_model = new $this->_modelName();
    }

    final private function __clone()
    {}
}
