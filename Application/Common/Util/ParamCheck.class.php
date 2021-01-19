<?php
namespace Common\Util;

use Think\Log;

class ParamCheck
{
	public static function checkNULL($key, $value)
	{
		if (is_null($value)) {
			Log("param [$key] cannot be null", 'DEBUG');
			E("[$key] 不能为空", C('PARAM_ERROR'));
		}
	}

	public static function checkNumber($key, $value, $min = NULL, $max = NULL)
	{
		if (!is_numeric($value)) {
			Log("param [ $key ] is not a number [ $value ]", 'DEBUG');
			E("[ $key ] 必须是数字", C('PARAM_ERROR'));
		}
		if (NULL !== $min && $value < $min) {
			Log("param [ $key ] is smaller than $min [ $value ]", 'DEBUG');
			E("[ $key ] 必须大于 $min", C('PARAM_ERROR'));
		}
		if (NULL !== $max && $value > $max) {
			Log("param [ $key ] is bigger than $max [ $value ]", 'DEBUG');
			E("[ $key ] 必须小于 $max", C('PARAM_ERROR'));
		}
	}

    /**
     * 数字长度 todo
     */
    public static function checkNumberLength($key, $value, $min=null, $max=null){
        if ( ! is_numeric ( $value ) )
        {
            E("{$key}必须为数字", '2');
        }

        if( !is_null($min) && !is_null($max)){
            $pattern = "/^\d{{$min},{$max}}$/";
            if ( !preg_match($pattern, $value) )
            {
                Log("参数 [ $key ] is smaller than $min [ $value ]", 'DEBUG');
                E("{$key}长度必在{$min}到{$max}位范围内", '2');
            }
        }elseif( !is_null($min) ){
            $pattern = "/^\d{{$min},}$/";
            if ( !preg_match($pattern, $value) )
            {
                Log("参数 [ $key ] is smaller than $min [ $value ]", 'DEBUG');
                E("{$key}长度不能小于{$min}位", '2');
            }
        }
        elseif( !is_null($max) ){
            $pattern = "/^\d{,{$max}}$/";
            if ( !preg_match($pattern, $value) )
            {
                Log("参数 [ $key ] is smaller than $min [ $value ]", 'DEBUG');
                E("{$key}长度不能大于{$max}位", '2');
            }
        }
    }

	public static function checkInt($key, $value, $min = NULL, $max = NULL)
	{
		if (!is_numeric($value)) {
			Log("param [ $key ] is not a number [ $value ]", 'DEBUG');
			E("[ $key ] 必须是数字", C('PARAM_ERROR'));
		}
		if (strval(intval($value)) !== strval($value)) {
			Log("param [ $key ] is not an integer [ $value ]", 'DEBUG');
			E("[ $key ] 必须是整数", C('PARAM_ERROR'));
		}
		if (NULL !== $min && $value < $min) {
			Log("param [ $key ] is smaller than $min [ $value ]", 'DEBUG');
			E("[ $key ] 必须大于 $min", C('PARAM_ERROR'));
		}
		if (NULL !== $max && $value > $max) {
			Log("param [ $key ] is bigger than $max [ $value ]", 'DEBUG');
			E("[ $key ] 必须小于 $max", C('PARAM_ERROR'));
		}
	}

	public static function checkString($key, $value, $min = NULL, $max = NULL)
	{
		if (!is_string($value)) {
			Log::record("param [ $key ] is not a string [ $value ]", 'DEBUG');
			E("[ $key ] 必须是字符串", C('PARAM_ERROR'));
		}
		if (NULL !== $min && strlen($value) < $min) {
			Log::record("param [ $key ] is shorter than $min [ $value ]", 'DEBUG');
			E("[ $key ] 必须长于 $min", C('PARAM_ERROR'));
		}
		if (NULL !== $max && strlen($value) > $max) {
			Log::record("param [ $key ] is longer than $max [ $value ]", 'DEBUG');
			E("[ $key ] 必须短于 $max", C('PARAM_ERROR'));
		}
	}

	public static function checkStringArray($array, $min = NULL, $max = NULL)
	{
		foreach ($array as $k => $v) {
			self::checkString($k, $v, $min, $max);
		}
	}

	public static function checkUrl($key, $value)
	{
		$pattern = '/^(http|https):\/\//i';
		$reg_pattern = array ('options' => array ('regexp' => $pattern ) );
		if (false === filter_var ( $value, FILTER_VALIDATE_REGEXP, $reg_pattern )) {
			Log::record("param [ $key ] is not a valid url [ $value ]", 'DEBUG');
			E("[ $key ] 必须是一个 url", C('PARAM_ERROR'));
		}
	}

	public static function checkArray($key, $value, $arrCheck)
	{
		if (!is_array($value)) {
			Log::record("param [ $key ] is not an array [" . print_r ( $value, true ) . " ]", 'DEBUG');
			E('', C('PARAM_ERROR'));
		}
		foreach ($arrCheck as $v) {
			if (!in_array($v, $value)) {
				Log::record("param [ $key ] contains no key/value [ $v ]", 'DEBUG');
				E('', C('PARAM_ERROR'));
			}
		}
	}

	public static function checkArrayKey($needCheck, $arrChecked)
	{
		if (!is_array($arrChecked)) {
			Log::record('param the param to be detected is not an array', 'DEBUG');
			E('', C('PARAM_ERROR'));
		}

		if (is_array($needCheck)) {
			foreach($needCheck as $value) {
				if (!array_key_exists($value, $arrChecked)) {
					Log::record("param $value does not exist in the array", 'DEBUG');
					E('', C('PARAM_ERROR'));
				}
			}
		} else {
			if (!array_key_exists($needCheck, $arrChecked)) {
				Log::record("param $needCheck does not exist in the array", 'DEBUG');
				E('', C('PARAM_ERROR'));
			}
		}
	}

	public static function checkArrayNull($arrChecked)
	{
		if (!is_array($arrChecked)) {
			Log::record('param the param to be detected is not an array', 'DEBUG');
			E('', C('PARAM_ERROR'));
		}
		if (0 >= count($arrChecked)) {
			E('', C('PARAM_ERROR'));
		}
	}
}
