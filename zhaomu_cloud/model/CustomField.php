<?php
namespace addons\zhaomu_cloud\model;

use think\Model;

/**
 * 自定义字段模型
 * Class CustomField
 * @package addons\zhaomu_cloud\model
 */
class CustomField extends Model
{
    // 设置表名
    protected $name = 'customfields';
    
    // 设置主键
    protected $pk = 'id';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
    
    // 字段类型转换
    protected $type = [
        'id' => 'integer',
        'relid' => 'integer',
        'adminonly' => 'integer',
        'required' => 'boolean',
        'showorder' => 'boolean',
        'showinvoice' => 'boolean',
        'sortorder' => 'integer',
        'create_time' => 'integer',
        'update_time' => 'integer',
        'upstream_id' => 'integer',
        'showdetail' => 'boolean'
    ];
    
    // 允许批量赋值的字段
    protected $field = [
        'id',
        'type',
        'relid',
        'fieldname',
        'fieldtype',
        'description',
        'fieldoptions',
        'regexpr',
        'adminonly',
        'required',
        'showorder',
        'showinvoice',
        'sortorder',
        'create_time',
        'update_time',
        'upstream_id',
        'showdetail'
    ];
    
    public function addOrExit($data = null)
    {
        $result = $this->where('fieldname', $data['fieldname'])->where('relid', $data['relid'])->find();
        if ($result) {
            
            return $result;
        }
        
        $data['type'] = 'product';
        //$data['relid'] = [''];
       // $data['fieldname'] = '朝暮数据';
        $data['fieldtype'] = 'text';
       // $data['description'] = '朝暮数据字段';
        $data['fieldoptions'] = '';
        $data['regexpr'] = '';
        $data['adminonly'] = 1;
        $data['required'] = 0;
        $data['showorder'] = 0;
        $data['showinvoice'] = 0;
        $data['sortorder'] = 0;
        $data['create_time'] = 1251193584;
        $data['update_time'] = 1251193584;
        $data['upstream_id'] = 0;
        $data['showdetail'] = 0;
        
        $result = new self();
        $result->save($data);
        return $result;
    }
}
