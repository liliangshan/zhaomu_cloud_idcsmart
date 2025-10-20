<?php
namespace addons\zhaomu_cloud\model;

use think\Model;

/**
 * 服务器分组模型
 * Class ServerGroup
 * @package addons\zhaomu_cloud\model
 */
class ServerGroup extends Model
{
    // 设置表名
    protected $name = 'server_groups';
    
    // 设置主键
    protected $pk = 'id';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
    
    // 字段类型转换
    protected $type = [
        'id' => 'integer',
        'mode' => 'integer',
        'capacity' => 'integer'
    ];
    
    // 允许批量赋值的字段
    protected $field = [
        'id',
        'name',
        'type',
        'system_type',
        'mode',
        'capacity'
    ];
    public function addOrExit($data=null){
        $result = $this->where('name', '朝暮数据')->find();
        if($result){
      
            return $result;
        }
        $data['name'] = '朝暮数据';
        $data['type'] = 'zhaomucloudapi';
        $data['system_type'] = 'normal';
        $data['mode'] = 0;
        $data['capacity'] = 0;
   
        $result = new self();
        $result->save($data);
        return $result;
    }
}
