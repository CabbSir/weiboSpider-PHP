<?php
namespace Common\Util;
/**
 * Created by PhpStorm.
 * User: kongqiang
 * Date: 2018/2/1
 * Time: 下午2:45
 */
use Countable;
class Validator
{

    private static $error;

    public static function validate($validators, $messages){
        foreach ($validators as $k => $v){
            $arr = explode("|",$v);

            // 检测数组
            if( strpos( $k, ".*." ) ){
                list($fKey, $sKey )= explode(".*.", $k);
                foreach(I($fKey) as $subv){
                    foreach ($arr as $rule){
                        $r = explode(":", $rule,2);
                        $method = 'validate'.ucfirst($r[0]);

                        if(!method_exists(self::class,$method) || self::$method( $subv[$sKey], isset($r[1])?$r[1]:null)){
                            continue;
                        }
                        self::_setError($k, $r[0], $messages);
                        return false;
                    }
                }
            }else{
                foreach ($arr as $rule){
                    $r = explode(":", $rule,2);
                    $method = 'validate'.ucfirst($r[0]);

                    if(!method_exists(self::class,$method) || self::$method(I($k,null), isset($r[1])?$r[1]:null)){
                        continue;
                    }
                    self::_setError($k, $r[0], $messages);
                    return false;
                }
            }
        }
        return true;
    }

    private static function _setError($key, $rule, $messages){
        if( array_key_exists($key.'.'.$rule, $messages) ){
            self::$error = $messages[$key.'.'.$rule];
        }elseif(array_key_exists($key, $messages)){
            self::$error = $messages[$key];
        }else{
            self::$error = self::defaultError($key, $rule);
        }
    }

    public static function validateRequired($value, $rule = null){
        if (empty($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif ((is_array($value) || $value instanceof Countable) && count($value) < 1) {
            return false;
        }
        return true;
    }

    public static function validateArray($value, $rule = null){
        if(isset($value)){
            return is_array($value);
        }
        return true;
    }

    public static function validateMinSizeOf($value, $rule = null){
        if(!empty($value)){
            return sizeof($value) >= $rule;
        }
        return true;
    }

    public static function validateMaxSizeOf($value, $rule = null){
        if(!empty($value)){
            return sizeof($value) <= $rule;
        }
        return true;
    }

    public static function validateMin($value, $rule = null){
        if(!empty($value)){
            return $value >= $rule;
        }
        return true;
    }

    public static function validateMax($value, $rule = null){
        if(!empty($value)){
            return $value <= $rule;
        }
        return true;
    }

    public static function validateIn($value, $rule = null){
        if(!empty($value)){
            return in_array($value, explode(',', $rule));
        }
        return true;
    }

    public static function validateString($value, $rule = null){
        if(isset($value)){
            return is_string($value);
        }
        return true;
    }

    /**
     * 允许中文、字母
     * @param $value
     * @param null $rule
     */
    public function validateChineseLetter($value, $rule = null){
        if(!empty($value)){
            return preg_match('/^[\x7f-\xffa-zA-Z]+$/',$value);
        }
        return true;
    }

    /**
     * 允许中文、字母
     * @param $value
     * @param null $rule
     */
    public function validateChineseLetterNum($value, $rule = null){
        if(!empty($value)){
//            return preg_match('/^[\u4E00-\u9FA5\uf900-\ufa2da-zA-Z0-9·s]+$/',$value);
            return preg_match('/^[\x7f-\xffa-zA-Z0-9·s]+$/',$value);
        }
        return true;
    }

    /**
     * 最大汉子长度
     * @param $value
     * @param null $rule
     * @return bool
     */
    public function validateMaxMbLen($value, $rule = null){
        if(!empty($value)){
            return mb_strlen($value, 'UTF-8') <= $rule;
        }
        return true;
    }

    /**
     * 最小汉子长度
     * @param $value
     * @param null $rule
     * @return bool
     */
    public function validateMinMbLen($value, $rule = null){
        if(!empty($value)){
            return mb_strlen($value, 'UTF-8') >= $rule;
        }
        return true;
    }


    public static function validateSame($value, $rule = null){
        if(!empty($value)){
            return $value == I($rule);
        }
        return true;
    }

    public static function validateReg($value, $rule = null){
        if(!empty($value)){
            return preg_match($rule, $value);
        }
        return true;
    }

    public static function validateMobile($value, $rule = null){
        if(!empty($value)){
            return preg_match('/^1(3\d|[4579]|5[012356789]|66|7[135678]|8\d|9[89])\d{8}$/',$value);
        }
        return true;
    }

    public static function validateMinStrLen($value, $rule = null){
        if(!empty($value)){
            return strlen($value) >= $rule;
        }
        return true;
    }

    public static function validateMaxStrLen($value, $rule = null){
        if(!empty($value)){
            return strlen($value) <= $rule;
        }
        return true;
    }

    public static function validateDecimal($value, $rule = null){
        if(!empty($value)){
            $result = preg_match('/^\d+(\.\d+)?$/', $value);
            if(!is_null($rule)){
                $nums = explode('.', $value);
                $rules = explode(',', $rule,2);
                if(isset($rules[0]) && $rules[0] != ''){
                    $result = $result && strlen(str_replace('.','',$value)) <= $rules[0];
                }
                if(isset($rules[1]) && $rules[1] != '' && isset($nums[1])){
                    $result = $result && strlen($nums[1]) <= $rules[1];
                }
            }
            return $result;
        }
        return true;
    }

    public static function validateIp($value, $rule = null){
        if(!empty($value)){
            return ip2long($value) > 0;
        }
        return true;
    }

    public static function validateInt($value, $rule = null){
        // int类型判断补充: 前端提交字段不填时为空字符串, 所以补充空字符串判断通过
        if(isset($value) && $value !== ''){
            return strval(intval($value)) === strval($value);
        }
        return true;
    }

    public static function validateQq($value, $rule = null){
        if(!empty($value)){
            return preg_match('/^[1-9]\d{5,11}$/', $value);
        }
        return true;
    }

    public static function validateWeixin($value, $rule = null){
        if(!empty($value)){
            return preg_match('/^[1-9]\d{5,11}$/', $value);
        }
        return true;
    }

    /**
     * 身份证号校验
     * @param null $code
     * @param string $errorMessage
     * @return bool
     */
    public static function validateIdentity ($value, $rule = null)
    {
        if(empty($value)) return true;

        $val = strtoupper($value);

        $arr_split = array();
        $pattern = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
        $matchFlag = preg_match($pattern, $val, $arr_split);
        if( !$matchFlag ){
            return false;
        }

        $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) {
            return false;
        }

        //检验18位身份证的校验码是否正确。
        //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
        $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $sign = 0;
        for ($i = 0; $i < 17; $i++) {
            $b = (int)$val{$i};
            $w = $arr_int[$i];
            $sign += $b * $w;
        }
        $n = $sign % 11;
        $val_num = $arr_ch[$n];
        if ($val_num != substr($val, 17, 1)) {
            return false;
        }

        return true;
    }

    /**
     * 校验密码
     * @param $value
     * @param null $rule
     * @return bool
     */
    public static function validatePassword($value){
        if(!empty($value)) {
            return preg_match('/^(?![^a-z]+$)(?![^A-Z]+$)(?!\D+$).{8,16}$/', $value);
        }
        return true;
    }

    /**
     * 校验密码
     * @param $value
     * @param null $rule
     * @return bool
     */
    public static function validateEmail($value){
        if(!empty($value)) {
            return preg_match('/^([0-9A-Za-z\-_\.]+)@([0-9a-z]+\.[a-z]{2,3}(\.[a-z]{2})?)$/', $value);

//            if( !filter_var($value, FILTER_VALIDATE_EMAIL)){
//                return false;
//            }
        }
        return true;
    }


    private static function defaultError($key, $rule = null){
        $method = 'default'.ucfirst($rule).'Error';
        if(!empty($rule) && method_exists(self::class,$method)){
            return self::$method($key);
        }
        //return '参数'.$key.'不正确';
        return ErrorMap::PARAM_INVALID;
    }

    private static function defaultRequiredError($key){
        //return "缺少参数".$key;
        return ErrorMap::PARAM_MISSING;
    }

    public static function getError(){
        return self::$error;
    }
}