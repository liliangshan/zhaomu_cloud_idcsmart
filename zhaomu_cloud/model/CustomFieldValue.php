<?php

namespace addons\zhaomu_cloud\model;

use think\Model;

/**
 * 自定义字段值模型
 * Class CustomFieldValue
 * @package addons\zhaomu_cloud\model
 */
class CustomFieldValue extends Model
{
    /**
     * 表名（不包含前缀）
     * @var string
     */
    protected $name = 'customfieldsvalues';
    
    /**
     * 主键
     * @var string
     */
    protected $pk = 'id';
    
    /**
     * 自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = false;
    
    /**
     * 字段类型转换
     * @var array
     */
    protected $type = [
        'id' => 'integer',
        'fieldid' => 'integer',
        'relid' => 'integer',
        'value' => 'string',
        'create_time' => 'integer',
        'update_time' => 'integer',
    ];
    
    /**
     * 允许批量赋值的字段
     * @var array
     */
    protected $field = [
        'fieldid',
        'relid', 
        'value',
        'create_time',
        'update_time',
    ];
    //关联CustomField
    public function customField()
    {
        return $this->belongsTo(CustomField::class, 'fieldid', 'id');
    }
    public function addOrExit($data = null)
    {
        $result = $this->where('fieldid', $data['fieldid'])->where('relid', $data['relid'])->find();
        if (!$result) {
            $result = new self();
        }
        $result->save($data);
        return $result;

    }
}
