<?php

namespace addons\zhaomu_cloud\model;

use think\Model;

/**
 * 产品模型
 * Class Product
 * @package addons\zhaomu_cloud\model
 */
class Product extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'products';
    
    // 设置主键
    protected $pk = 'id';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;
    
    // 字段类型转换
    protected $type = [
        'id' => 'integer',
        'gid' => 'integer',
        'is_domain' => 'boolean',
        'hidden' => 'boolean',
        'show_domain_options' => 'boolean',
        'welcome_email' => 'integer',
        'stock_control' => 'boolean',
        'qty' => 'integer',
        'prorata_billing' => 'boolean',
        'prorata_date' => 'integer',
        'prorata_charge_next_month' => 'integer',
        'allow_qty' => 'integer',
        'server_group' => 'integer',
        'recurring_cycles' => 'integer',
        'auto_terminate_days' => 'integer',
        'auto_terminate_email' => 'integer',
        'config_options_upgrade' => 'boolean',
        'upgrade_email' => 'integer',
        'down_configoption_refund' => 'integer',
        'overages_disk_limit' => 'integer',
        'overages_bw_limit' => 'integer',
        'overages_disk_price' => 'float',
        'overages_bw_price' => 'float',
        'tax' => 'boolean',
        'affiliateonetime' => 'boolean',
        'affiliate_pay_amount' => 'float',
        'order' => 'integer',
        'retired' => 'boolean',
        'is_featured' => 'boolean',
        'create_time' => 'integer',
        'update_time' => 'integer',
        'auto_create_config_options' => 'integer',
        'groupid' => 'integer',
        'location_version' => 'integer',
        'upstream_version' => 'integer',
        'upstream_price_value' => 'float',
        'zjmf_api_id' => 'integer',
        'upstream_pid' => 'integer',
        'is_truename' => 'integer',
        'p_uid' => 'integer',
        'rate' => 'float',
        'clientscount' => 'integer',
        'upper_reaches_id' => 'integer',
        'is_bind_phone' => 'integer',
        'app_hot_order' => 'integer',
        'app_hot_lock' => 'boolean',
        'app_hot_heat' => 'integer',
        'app_recommend_status' => 'boolean',
        'app_recommend_order' => 'integer',
        'app_recommend_lock' => 'boolean',
        'app_pay_type' => 'boolean',
        'app_score' => 'float',
        'app_status' => 'boolean',
        'upstream_qty' => 'integer',
        'cancel_control' => 'boolean',
        'unretired_time' => 'integer',
        'upstream_stock_control' => 'boolean',
        'upstream_ontrial_status' => 'boolean',
        // JSON格式字段设置为array
        'host' => 'array',
        'password' => 'array',
        'pay_type' => 'array',
        'overages_enabled' => 'array',
        'affiliate_pay_type' => 'array',
        'billing_cycle_upgrade' => 'array',
        'upstream_auto_setup' => 'array'
    ];
    
    // 允许批量赋值的字段
    protected $field = [
        'id', 'type', 'gid', 'name', 'description', 'host', 'is_domain',
        'hidden', 'password', 'show_domain_options', 'welcome_email',
        'stock_control', 'qty', 'prorata_billing', 'prorata_date',
        'prorata_charge_next_month', 'pay_type', 'pay_method', 'allow_qty',
        'auto_setup', 'server_type', 'server_group', 'config_option1',
        'config_option2', 'config_option3', 'config_option4', 'config_option5',
        'config_option6', 'config_option7', 'config_option8', 'config_option9',
        'config_option10', 'config_option11', 'config_option12', 'config_option13',
        'config_option14', 'config_option15', 'config_option16', 'config_option17',
        'config_option18', 'config_option19', 'config_option20', 'config_option21',
        'config_option22', 'config_option23', 'config_option24', 'recurring_cycles',
        'auto_terminate_days', 'auto_terminate_email', 'config_options_upgrade',
        'billing_cycle_upgrade', 'upgrade_email', 'down_configoption_refund',
        'overages_enabled', 'overages_disk_limit', 'overages_bw_limit',
        'overages_disk_price', 'overages_bw_price', 'tax', 'affiliateonetime',
        'affiliate_pay_type', 'affiliate_pay_amount', 'order', 'retired',
        'is_featured', 'create_time', 'update_time', 'auto_create_config_options',
        'groupid', 'api_type', 'location_version', 'upstream_version',
        'upstream_price_type', 'upstream_price_value', 'zjmf_api_id', 'upstream_pid',
        'is_truename', 'uuid', 'p_uid', 'rate', 'clientscount', 'app_type',
        'product_shopping_url', 'product_group_url', 'upper_reaches_id',
        'version_description', 'app_version', 'is_bind_phone', 'app_hot_order',
        'app_hot_lock', 'app_hot_heat', 'app_recommend_status', 'app_recommend_order',
        'app_recommend_lock', 'app_pay_type', 'app_score', 'app_images',
        'app_status', 'upstream_qty', 'upstream_product_shopping_url',
        'cancel_control', 'unretired_time', 'upstream_stock_control',
        'upstream_auto_setup', 'upstream_ontrial_status'
    ];
    private function setProToNav($param, $id)
	{
		$menu = new \app\common\logic\Menu();
		$menu_list = $menu->getOneNavs("client", null);
		if (!$param["ptype"] || !isset($menu_list[$param["ptype"]])) {
			throw new \Exception("前台导航页面不存在！");
		}
		foreach ($menu_list as $key => $val) {
			if ($val["nav_type"] == 2) {
				$relid = explode(",", $val["relid"]);
				$is_exits = array_search($id, $relid);
				if ($is_exits !== false) {
					unset($relid[$is_exits]);
					$menu_list[$key]["relid"] = implode(",", $relid);
				}
			}
		}
		$p_relid = explode(",", $menu_list[$param["ptype"]]["relid"]);
		$menu_list[$param["ptype"]]["relid"] = implode(",", array_merge($p_relid, [$id]));
		return $menu->editDefaultNav($menu_list);
	}
    public function addOrExit($data = null)
    {
        (new Server())->addOrExit();
        $serverGroup = (new ServerGroup())->addOrExit();
        $result = $this->where('config_option1', $data['config_option1'])->where('server_group', $serverGroup->id)->find();
        if ($result) {
           
            $result->auto_setup = '';
            $result->save();

            $this->setProToNav(['ptype' => HlwidcCache::value('zhaomu_navigation_menu', null) ], $result->id);

            return $result;
        }
        $data['config_option1'] = $data['config_option1'];
        $data['name'] = $data['name'];
        $data['hidden'] = 1;
        $data['gid'] = (new ProductGroup())->addOrExit()->id;
        $data['type'] = 'cloud';
        $data['create_time'] = 1251193584;
        $data['update_time'] = 1251193584;
        $data['pay_method'] = 'prepayment';
        $data['auto_setup'] = '';
        $data['api_type'] = 'normal';
        $data['location_version'] = 1;
        $data['upstream_version'] = 0;
        $data['upstream_price_type'] = 'percent';
        $data['upstream_price_value'] = 120.00;
        $data['zjmf_api_id'] = 0;
        $data['upstream_pid'] = 0;
        $data['is_truename'] = 0;
        $data['p_uid'] = 0;
        $data['rate'] = 1.00;
        $data['clientscount'] = 0;
        $data['upper_reaches_id'] = 0;
        $data['is_bind_phone'] = 0;
        $data['app_hot_order'] = 0;
        $data['app_hot_lock'] = 0;
        $data['app_hot_heat'] = 0;
        $data['app_recommend_status'] = 0;
        $data['app_recommend_order'] = 0;
        $data['app_recommend_lock'] = 0;
        $data['app_pay_type'] = 0;
        $data['server_group'] = (new ServerGroup())->addOrExit()->id;
        $data['pay_type'] = ["pay_type" => "recurring", "pay_hour_cycle" => 0, "pay_day_cycle" => 0, "pay_ontrial_status" => 0, "pay_ontrial_cycle" => 0, "pay_ontrial_condition" => [], "pay_ontrial_cycle_type" => "day"];
        $data['app_score'] = 0;
        $data['app_status'] = 0;
        $data['unretired_time'] = 0;
        $data['upstream_stock_control'] = 0;
        $data['upstream_ontrial_status'] = 0;
        
        $result = new self();
        $result->save($data);
       
        $this->setProToNav(['ptype' => HlwidcCache::value('zhaomu_navigation_menu', null) ], $result->id);
        return $result;
    }
}
