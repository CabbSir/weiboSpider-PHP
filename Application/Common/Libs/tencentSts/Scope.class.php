<?php

namespace Common\Libs\tencentSts;

class Scope{
	var $action;
	var $bucket;
	var $region;
	var $resourcePrefix;
	function __construct($action, $bucket, $region, $resourcePrefix){
		$this->action = $action;
		$this->bucket = $bucket;
		$this->region = $region;
		$this->resourcePrefix = $resourcePrefix;
	}
	function get_action(){
		return $this->action;
	}
	
	function get_resource(){
		$index = strripos($this->bucket, '-');
		$bucketName = substr($this->bucket, 0, $index);
		$appid = substr($this->bucket, $index + 1);
		if(!(strpos($this->resourcePrefix, '/') === 0)){
			$this->resourcePrefix = '/' . $this->resourcePrefix;
		}
		return 'qcs::cos:' . $this->region . ':uid/' . $appid . ':prefix//' . $appid . '/' . $bucketName . $this->resourcePrefix;
	}
}
