<?php
namespace addons\zhaomu_cloud\controller\clientarea;
use addons\zhaomu_cloud\ZhaoMuTemplete;
/**
 * 用户端控制器
 * Class IndexController
 * @package addons\zhaomu_cloud\controller\clientarea
 */
class IndexController extends Base
{
  
 public function index(){

     $this->assign('zhaomu_cloud_template',(new ZhaoMuTemplete())->fetch());
     return $this->fetch('/index');

 }

}