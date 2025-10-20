<?php

namespace addons\zhaomu_cloud\controller\clientarea;

use app\home\controller\PluginHomeBaseController;

/**
 * 基础控制器
 * Class Base
 * @package addons\wechat_common_notify\controller\clientarea
 */
class Base extends PluginHomeBaseController
{
    public $data, $theme = "";
    
    public function initialize()
    {
        parent::initialize();
    }
    
    /**
     * 成功响应
     * @param mixed $arr 响应数据
     */
    public function success($arr)
    {
        echo json_encode([
            'status' => 1,
            'encrypt' => 1,
            'info' => $arr
        ], JSON_UNESCAPED_UNICODE);
        die;
    }
    
    /**
     * 错误响应
     * @param mixed $arr 错误信息
     */
    public function error($arr)
    {
        echo json_encode([
            'status' => 0,
            'info' => $arr
        ], JSON_UNESCAPED_UNICODE);
        die;
    }
}
