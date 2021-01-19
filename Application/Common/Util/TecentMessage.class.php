<?php


namespace Common\Util;
use Exception;
use Qcloud\Sms\SmsSingleSender;

class TecentMessage
{
    // 短信应用SDK AppID
    private $appId = 1400183846; // 1400开头

    // 短信应用SDK AppKey
    private $appKey = "d2696c77cb86463ee5c7838833ba3ef9";

    private $templateId = 317186;

    private $smsSign = "先生的世界";

    public function registerMessage($phone, $code)
    {
        try {
            $ssender = new SmsSingleSender($this->appId, $this->appKey);
            $result = $ssender->sendWithParam("86", $phone, $this->templateId,
                [$code], $this->smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
            $result = json_decode($result, true);
            if ($result['errmsg'] == 'OK') {
                return true;
            } else {
                return false;
            }
        } catch(Exception $e) {
            echo var_dump($e);
        }
    }
}