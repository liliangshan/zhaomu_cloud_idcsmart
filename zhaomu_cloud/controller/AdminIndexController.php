<?php
namespace addons\zhaomu_cloud\controller;
use app\admin\controller\PluginAdminBaseController;
use addons\zhaomu_cloud\ZhaoMuTemplete;
class AdminIndexController extends PluginAdminBaseController
{
  

    public function index(){


        $this->assign('Title','朝暮云模块配置');
        $this->assign('zhaomu_cloud_template',(new ZhaoMuTemplete())->fetch());
        return $this->fetch('/index');
    }

    


}