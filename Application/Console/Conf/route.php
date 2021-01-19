<?php

return [
    'URL_ROUTER_ON'   => true,
    'URL_ROUTE_RULES' => [
    ],
    'URL_MAP_RULES'   => [
        'proxy/fresh' => 'Proxy/freshProxyInfo',
        'proxy/test'  => 'Proxy/testProxySpeed',

        'user/info'   => 'User/getUserInfo',
        'user/update' => 'User/updateUserInfo',

        'content/history' => 'Content/getHistoryWb',
        'content/update'  => 'Content/getUpdate',
        'content/send'    => 'Content/email',
    ]
];