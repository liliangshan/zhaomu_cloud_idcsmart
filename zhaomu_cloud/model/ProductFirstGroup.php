<?php
namespace addons\zhaomu_cloud\model;

use think\Model;

/**
 * 产品一级分组模型
 * Class ProductFirstGroup
 * @package addons\zhaomu_cloud\model
 */
class ProductFirstGroup extends Model
{
    // 设置表名
    protected $name = 'product_first_groups';
    
    // 设置主键
    protected $pk = 'id';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
    

    
    // 字段类型转换
    protected $type = [
        'id' => 'integer',
        'hidden' => 'boolean',
        'order' => 'integer',
        'create_time' => 'integer',
        'update_time' => 'integer',
        'is_upstream' => 'boolean',
        'zjmf_api_id' => 'integer'
    ];
    
    // 允许批量赋值的字段
    protected $field = [
        'id',
        'name',
        'hidden',
        'order',
        'create_time',
        'update_time',
        'is_upstream',
        'zjmf_api_id'
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
        $result = new self();
        $result->save($data);
        return $result;

    }
}
