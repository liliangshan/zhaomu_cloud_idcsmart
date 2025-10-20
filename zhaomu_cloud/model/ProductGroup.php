<?php
namespace addons\zhaomu_cloud\model;

use think\Model;

/**
 * 产品分组模型
 * Class ProductGroup
 * @package addons\zhaomu_cloud\model
 */
class ProductGroup extends Model
{
    // 设置表名
    protected $name = 'product_groups';
    
    // 设置主键
    protected $pk = 'id';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
    
    // 字段类型转换
    protected $type = [
        'id' => 'integer',
        'hidden' => 'boolean',
        'order' => 'integer',
        'type' => 'integer',
        'create_time' => 'integer',
        'update_time' => 'integer',
        'gid' => 'integer',
        'is_upstream' => 'boolean',
        'zjfm_api_id' => 'integer'
    ];
    
    // 允许批量赋值的字段
    protected $field = [
        'id',
        'name',
        'headline',
        'tagline',
        'order_frm_tpl',
        'disabled_gateways',
        'hidden',
        'order',
        'type',
        'create_time',
        'update_time',
        'gid',
        'tpl_type',
        'alias',
        'is_upstream',
        'zjfm_api_id'
    ];
    public function addOrExit($data=null){
        
        $result = $this->where('create_time', '1251193584')->find();
        if($result){
            if(isset($data['name'])){
            $result->name = $data['name'];
                $result->save();
            }
          
            return $result;
        }
        $data['name'] = '朝暮数据';
        $data['hidden'] = 1;
        $data['order'] = 99999;
        $data['is_upstream'] = 0;
        $data['zjmf_api_id'] = 0;
        $data['create_time'] = '1251193584';
        $data['update_time'] = '1251193584';
        $data['gid'] = (new ProductFirstGroup())->addOrExit()->id;
        $data['tpl_type'] = 0;
        $data['alias'] = '';
      
        $data['headline'] = '';
        $data['tagline'] = '';
        $data['order_frm_tpl'] = 0;
        $data['disabled_gateways'] = '';
        $result = new self();
        $result->save($data);
        return $result;
    }
}
