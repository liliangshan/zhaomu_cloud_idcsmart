<?php
namespace addons\zhaomu_cloud\model;

use think\Model;

/**
 * 发票模型
 * Class Invoice
 * @package addons\zhaomu_cloud\model
 */
class Invoice extends Model
{
    // 设置表名
    protected $name = 'invoices';
    
    // 设置主键
    protected $pk = 'id';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
    
    // 字段类型转换
    protected $type = [
        'id' => 'integer',
        'uid' => 'integer',
        'create_time' => 'integer',
        'update_time' => 'integer',
        'due_time' => 'integer',
        'paid_time' => 'integer',
        'last_capture_attempt' => 'integer',
        'subtotal' => 'float',
        'credit' => 'float',
        'tax' => 'float',
        'tax2' => 'float',
        'total' => 'float',
        'taxrate' => 'float',
        'taxrate2' => 'float',
        'delete_time' => 'integer',
        'due_email_times' => 'integer',
        'aff_sure_time' => 'integer',
        'aff_commission' => 'float',
        'aff_commmission_bates' => 'float',
        'aff_commmission_bates_type' => 'integer',
        'is_aff' => 'integer',
        'is_cron' => 'boolean',
        'suffix' => 'integer',
        'use_credit_limit' => 'boolean',
        'invoice_id' => 'integer',
        'is_delete' => 'boolean',
        'credit_limit_prepayment' => 'boolean'
    ];
    
    // 允许批量赋值的字段
    protected $field = [
        'id',
        'uid',
        'invoice_num',
        'create_time',
        'update_time',
        'due_time',
        'paid_time',
        'last_capture_attempt',
        'subtotal',
        'credit',
        'tax',
        'tax2',
        'total',
        'taxrate',
        'taxrate2',
        'status',
        'payment',
        'notes',
        'delete_time',
        'due_email_times',
        'type',
        'payment_status',
        'aff_sure_time',
        'aff_commission',
        'aff_commmission_bates',
        'aff_commmission_bates_type',
        'is_aff',
        'is_cron',
        'suffix',
        'use_credit_limit',
        'invoice_id',
        'url',
        'paymt',
        'is_delete',
        'credit_limit_prepayment',
        'credit_limit_prepayment_invoices'
    ];
    //关联item
    public function item()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id')->where('type', 'product')->whereOr('type', 'renew')->with('host');
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
        $data['invoice_num'] = 'INV-' . date('YmdHis') . '-' . rand(1000, 9999);
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['due_time'] = $nextDueTime; // 30天后到期
        $data['paid_time'] = 0;
        $data['last_capture_attempt'] = 0;
        $data['subtotal'] = $data['subtotal'] ?? 0.00;
        $data['credit'] = $data['credit'] ?? 0.00;
        $data['tax'] = 0.00;
        $data['tax2'] = 0.00;
        $data['total'] = $data['total'] ?? 0.00;
        $data['taxrate'] = 0.00;
        $data['taxrate2'] = 0.00;
        $data['status'] = $data['status'] ?? 'Unpaid';
        $data['payment'] = $data['payment'] ?? 'monthly';
        $data['notes'] = '';
        $data['delete_time'] = 0;
        $data['due_email_times'] = 0;
        $data['type'] = 'product';
        $data['payment_status'] = $data['payment_status'] ?? 'pending';
        $data['aff_sure_time'] = null;
        $data['aff_commission'] = null;
        $data['aff_commmission_bates'] = null;
        $data['aff_commmission_bates_type'] = null;
        $data['is_aff'] = 0;
        $data['is_cron'] = 0;
        $data['suffix'] = 0;
        $data['use_credit_limit'] = 0;
        $data['invoice_id'] = 0;
        $data['url'] = '';
        $data['paymt'] = '';
        $data['is_delete'] = 0;
        $data['credit_limit_prepayment'] = 0;
        $data['credit_limit_prepayment_invoices'] = '';
        
        $result = new self();
        $result->save($data);
        return $result;
    }
}
