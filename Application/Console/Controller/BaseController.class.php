<?php

namespace Console\Controller;

use Common\Util\ErrorMap;
use Common\Util\Validator;

class BaseController extends \Common\Controller\BaseController
{
    // 用户信息
    protected $user = null;

    // 时间
    protected $_start;
    protected $_end;
    protected $_startTs;
    protected $_endTs;

    // 默认分页编号
    protected $_defaultPageNo = 1;
    // 每页默认数量
    protected $_defaultPageSize = 5;
    // 每页最大数量
    protected $_maxPageSize = 100;

    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * @param $rules
     * @param array $messages
     */
    protected function validate($rules, $messages = [])
    {
        if (!Validator::validate($rules, $messages)) {
            $this->error(Validator::getError());
        }
    }

    /**
     * 获取分页参数
     * @param $totalNum
     * @param null $pageNo
     * @param null $pageSize
     * @return array
     */
    protected function _getPageInfo($totalNum, $pageNo = null, $pageSize = null)
    {
        $pageNo   = isset($pageNo) ? (int)$pageNo : I('get.pageNo', $this->_defaultPageNo, 'int');
        $pageSize = isset($pageSize) ? (int)$pageSize : I('get.pageSize', $this->_defaultPageSize, 'int');

        $pageNo <= 0 && $pageNo = $this->_defaultPageNo;
        $pageSize > $this->_maxPageSize && $pageSize = $this->_maxPageSize;

        return [
            'pageNo'   => $pageNo,
            'pageSize' => $pageSize,
            'totalNum' => (int)$totalNum
        ];
    }
}