<?php
// vim: set expandtab cindent tabstop=4 shiftwidth=4 fdm=marker:
// +----------------------------------------------------------------------+
// | The Wanka Inc                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 2015, Wanka Inc. All rights reserved.                  |
// +----------------------------------------------------------------------+
// | Authors: The PHP Dev Team, BY xuechuanchuan.                         |
// | Descript:                                                            |
// +----------------------------------------------------------------------+ 

/**
 * @email    xuechuanchuan@gm825.com
 * @descript  **
 * @author   xuechuanchuan
 */

namespace Think\Upload\Driver;

require_once __DIR__ . '/Cos/vendor/autoload.php';

class Cos
{
    /**
     * 上传文件根目录
     * @var string
     */
    private $rootPath;

    /**
     * 上传错误信息
     * @var string
     */
    private $error = '';

    private $cos;

    private $config = [];

    /**
     * 构造函数，用于设置上传根路径
     * @param string $root 根目录
     * @param array $config FTP配置
     */
    public function __construct($root, $config)
    {
        $this->config = array_merge($this->config, $config);

        /* 设置根目录 */
        $this->rootPath = trim($root, './') . '/';

        $this->cos = new \Qcloud\Cos\Client([
            'region'      => $this->config['region'], #地域，如ap-guangzhou,ap-beijing-1
            'credentials' => [
                'secretId'  => $this->config['secret_id'],
                'secretKey' => $this->config['secret_key'],
            ],
        ]);
    }

    /**
     * 检测上传根目录(七牛上传时支持自动创建目录，直接返回)
     * @return boolean true-检测通过，false-检测失败
     */
    public function checkRootPath()
    {
        return true;
    }

    /**
     * 检测上传目录(七牛上传时支持自动创建目录，直接返回)
     * @param  string $savepath 上传目录
     * @return boolean          检测结果，true-通过，false-失败
     */
    public function checkSavePath($savepath)
    {
        return true;
    }

    /**
     * 创建文件夹 (七牛上传时支持自动创建目录，直接返回)
     * @param  string $savepath 目录名称
     * @return boolean          true-创建成功，false-创建失败
     */
    public function mkdir($savepath)
    {
        return true;
    }

    /**
     * 保存指定文件
     * @param  array $file 保存的文件信息
     * @param  bool $replace 同名文件是否覆盖
     * @return boolean          保存状态，true-成功，false-失败
     */
    public function save(&$file, $replace = true)
    {
        $file['name'] = $file['savepath'] . $file['savename'];

        $file['content'] = fopen($file['tmp_name'], 'r');

        $url = $this->upload($file);

        fclose($file['content']);

        $file['url'] = $url;

        return false === $url ? false : true;
    }

    public function upload($file)
    {
        if (!$file['content']) return false;

        $uploadName = $file['name'] ? $file['name'] : rtrim($this->config['resource_prefix'], '*') . date('Y-m-d') . '/' . uniqid() . '.' . $this->_getImgType($file['content']);

        try {
            $result = $this->cos->putObject([
                'Bucket' => $this->config['bucket'],
                'Key'    => $uploadName,
                'Body'   => $file['content']
            ]);

            return C('UPLOAD_FILE_DOMAIN') ? C('UPLOAD_FILE_DOMAIN') . '/' . ltrim($uploadName, '/') : $result['ObjectURL'];
        } catch (\Exception $e) {
            $this->error = $e->getMessage();

            return false;
        }
    }

    private function _getImgType($header)
    {
        if ($header{0} . $header{1} == "\x89\x50") {
            return 'png';
        } elseif ($header{0} . $header{1} == "\xff\xd8") {
            return 'jpeg';
        } elseif ($header{0} . $header{1} . $header{2} == "\x47\x49\x46") {
            return 'gif';
        }

        return 'jpg';
    }

    public function getError()
    {
        return $this->error;
    }
}

