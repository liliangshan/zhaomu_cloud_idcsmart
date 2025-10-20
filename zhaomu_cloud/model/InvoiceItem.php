<?php
namespace addons\zhaomu_cloud\model;

use think\Model;

/**
 * 发票项目模型
 * Class InvoiceItem
 * @package addons\zhaomu_cloud\model
 */
class InvoiceItem extends Model
{
    // 设置表名
    protected $name = 'invoice_items';
    
    // 设置主键
    protected $pk = 'id';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
    
    // 字段类型转换
    protected $type = [
        'id' => 'integer',
        'invoice_id' => 'integer',
        'uid' => 'integer',
        'rel_id' => 'integer',
        'amount' => 'float',
        'taxed' => 'boolean',
        'due_time' => 'integer',
        'delete_time' => 'integer',
        'aff_sure_time' => 'integer',
        'aff_commission' => 'float',
        'aff_commmission_bates' => 'float',
        'aff_commmission_bates_type' => 'integer',
        'is_aff' => 'integer'
    ];
    
    // 允许批量赋值的字段
    protected $field = [
        'id',
        'invoice_id',
        'uid',
        'type',
        'rel_id',
        'description',
        'description2',
        'amount',
        'taxed',
        'due_time',
        'payment',
        'notes',
        'delete_time',
        'aff_sure_time',
        'aff_commission',
        'aff_commmission_bates',
        'aff_commmission_bates_type',
        'is_aff'
    ];
    //关联host
    public function host()
    {
        return $this->belongsTo(Host::class, 'rel_id', 'id')->field('id,domain,domainstatus,productid')->with('product');
    }
    public function addOrExit($data = null)
    {
       
         // 根据计费周期设置到期时间
         $billingCycle = $data['billingcycle'] ?? 'monthly';
         $currentTime = time();
         
         switch ($billingCycle) {
             case 'monthly':
                 // 按月计算，使用 date() 函数精确计算
                 $nextDueTime = strtotime('+1 month', $currentTime);
                 break;
             case 'quarterly':
                 // 按季度计算，3个月
                 $nextDueTime = strtotime('+3 months', $currentTime);
                 break;
             case 'yearly':
                 // 按年计算，使用 date() 函数精确计算
                 $nextDueTime = strtotime('+1 year', $currentTime);
                 break;
             case 'weekly':
                 // 按周计算
                 $nextDueTime = strtotime('+1 week', $currentTime);
                 break;
             case 'daily':
                 // 按天计算
                 $nextDueTime = strtotime('+1 day', $currentTime);
                 break;
             default:
                 // 默认月付
                 $nextDueTime = strtotime('+1 month', $currentTime);
                 break;
         }
        $data['type'] = 'product';
        $data['uid'] = $data['uid'] ?? 1;
        $data['description'] = $data['description'] ?? '朝暮数据产品';
        $data['description2'] = '';
        $data['amount'] = $data['amount'] ?? 0.00;
        $data['taxed'] = $data['taxed'] ?? 0;
        $data['due_time'] = $nextDueTime; // 30天后到期
        $data['payment'] = $data['payment'] ?? 'monthly';
        $data['notes'] = '';
        $data['delete_time'] = 0;
        $data['aff_sure_time'] = null;
        $data['aff_commission'] = null;
        $data['aff_commmission_bates'] = null;
        $data['aff_commmission_bates_type'] = null;
        $data['is_aff'] = 0;
        
        $result = new self();
        $result->save($data);
        return $result;
    }
}
