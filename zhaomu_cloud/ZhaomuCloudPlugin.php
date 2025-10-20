<?php

namespace addons\zhaomu_cloud;

use app\admin\lib\Plugin;
use Think\Db;
use addons\zhaomu_cloud\services\LogService;

class ZhaomuCloudPlugin extends Plugin
{
    # 插件基本信息
    public $info = array(
        'name'        => 'ZhaomuCloud',
        'title'       => '朝暮数据，全球最大云主机服务商',
        'description' => '朝暮数据，全球最大云主机服务商',
        'status'      => 1,
        'author'      => '朝暮数据',
        'version'     => '1.0.0',
        'module'     => 'addons'
    );

    # 插件安装
    public function install()
    {
        $DbConfig = Db::getConfig();

        // 创建缓存表
        $cacheDbName = $DbConfig["prefix"] . "hlwidc_com_cache";
        $tableExists = Db::query("SHOW TABLES LIKE '{$cacheDbName}'");
        if (empty($tableExists)) {
            // 创建表的SQL语句
            $sql = "CREATE TABLE `{$cacheDbName}` (
                `set_key` varchar(100) NOT NULL COMMENT '缓存键',
                `set_value` longtext COMMENT '缓存值',
                PRIMARY KEY (`set_key`) USING BTREE,
                UNIQUE KEY `set_key` (`set_key`) USING BTREE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='朝暮云缓存表'";

            // 执行创建表语句
            Db::query($sql);
        }
        try {
            //判断CMF_ROOT.'/public/plugins/servers/zhaomucloudapi/zhaomucloudapi.php' 文件是否存在
            if (!file_exists(CMF_ROOT . '/public/plugins/servers/zhaomucloudapi/zhaomucloudapi.php')) {
                //复制zhaomucloudapi文件夹
                //判断文件夹是否存在
                if (!is_dir(CMF_ROOT . '/public/plugins/servers/zhaomucloudapi')) {
                    mkdir(CMF_ROOT . '/public/plugins/servers/zhaomucloudapi', 0755, true);
                }
                //复制zhaomucloudapi文件夹
                copy(dirname(__FILE__) . '/zhaomucloudapi/zhaomucloudapi.php', CMF_ROOT . '/public/plugins/servers/zhaomucloudapi/zhaomucloudapi.php');
                //复制模版文件
                if (!is_dir(CMF_ROOT . '/public/plugins/servers/zhaomucloudapi/templates')) {
                    mkdir(CMF_ROOT . '/public/plugins/servers/zhaomucloudapi/templates', 0755, true);
                }
                copy(dirname(__FILE__) . '/zhaomucloudapi/templates/information.html', CMF_ROOT . '/public/plugins/servers/zhaomucloudapi/templates/information.html');
            }
            return true;
        } catch (\Exception $e) {
            return "请先删除/public/plugins/servers/zhaomucloudapi/文件夹";
        }
    }
    # 插件卸载
    public function uninstall()
    {
        return true;
    }
}
