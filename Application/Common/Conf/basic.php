<?php

return [
    // 数据库设置
    'DB_TYPE'             => 'pdo',        // 数据库类型
    'DB_HOST'             => '127.0.0.1',     // 服务器地址
    'DB_NAME'             => 'weibo',              // 数据库名
    'DB_USER'             => 'root',          // 用户名
    'DB_PWD'              => 'root',          // 密码
    'DB_PORT'             => '3306',          // 端口
    'DB_PREFIX'           => 'tb_',            // 数据库表前缀
    'DB_FIELDTYPE_CHECK'  => false,           // 是否进行字段类型检查
    'DB_FIELDS_CACHE'     => false,           // 启用字段缓存
    'DB_CHARSET'          => 'utf8mb4',       // 数据库编码默认采用utf8mb4
    'DB_DEPLOY_TYPE'      => 0,               // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'      => false,           // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'       => 1,               // 读写分离后 主服务器数量
    'DB_SLAVE_NO'         => '',              // 指定从服务器序号
    'DB_SQL_BUILD_CACHE'  => false,           // 数据库查询的SQL创建缓存
    'DB_SQL_BUILD_QUEUE'  => 'file',          // SQL缓存队列的缓存方式 支持 file xcache和apc
    'DB_SQL_BUILD_LENGTH' => 20,              // SQL缓存的队列长度
    'DB_SQL_LOG'          => false,           // SQL执行日志记录
    'DB_BIND_PARAM'       => false,           // 数据库写入数据自动参数绑定

    // 缓存相关配置
    'DATA_CACHE_TYPE'     => 'redis',     // Redis,Memcache,File
    'REDIS_HOST'          => '127.0.0.1', // Redis 服务器地址
    'REDIS_PORT'          => 6379,        // Redis 端口
    'DATA_CACHE_TIMEOUT'  => 3,
    'DATA_CACHE_TIME'     => 24*60*60,        // 设置为24小时
    'DATA_CACHE_PREFIX'   => '',      // Redis key 前缀
    'DATA_CACHE_DB'       => 1,
    'DB_NO_DATA'          => -1,
    'CACHE_TO_DB_MOD'     => 3,

    'SESSION_OPTIONS'        => [

    ],
];
