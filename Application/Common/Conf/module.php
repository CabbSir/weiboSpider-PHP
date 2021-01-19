<?php

return [
    'DEFAULT_MODULE'     => 'Web',
    'DEFAULT_CONTROLLER' => 'View',
    'DEFAULT_ACTION'     => 'index',
    'MODULE_DENY_LIST'   => [],      // 禁止访问的模块
    'MODULE_ALLOW_LIST'  => ['Console', 'Web'], // 允许访问的模块
];
