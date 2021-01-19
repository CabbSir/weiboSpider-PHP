<?php
/**
 * @author chensisong@gm825.com
 */

require 'vendor/autoload.php';

$cosClient = new Qcloud\Cos\Client(array(
    'region' => 'ap-beijing', #地域，如ap-guangzhou,ap-beijing-1
    'credentials' => array(
        'secretId' => 'AKIDPegV4Q1P2OUKuHgJ3arvqX5RZTAePBC5',
        'secretKey' => 'skbMxLYXJzVqysZ5OUM9ufDVZNuA9rJX',
    ),
));

// 若初始化 Client 时未填写 appId，则 bucket 的命名规则为{name}-{appid} ，此处填写的存储桶名称必须为此格式
$bucket = 'bj-test-cos-1254456149';
// $bucket = 'ceshi-1254456149';
$key = 'a.txt';

# 上传文件
## putObject(上传接口，最大支持上传5G文件)
### 上传内存中的字符串
try {
    $result = $cosClient->putObject(array(
        'Bucket' => $bucket,
        'Key' => $key,
        'Body' => 'Hello World12!'
    ));
    print_r($result);
    # 可以直接通过$result读出返回结果
    echo ($result['ETag']);
} catch (\Exception $e) {
    echo($e);
}