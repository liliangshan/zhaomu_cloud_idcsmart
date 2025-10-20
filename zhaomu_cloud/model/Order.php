<?php
namespace addons\zhaomu_cloud\model;

use think\Model;

/**
 * 订单模型
 * Class Order
 * @package addons\zhaomu_cloud\model
 */
class Order extends Model
{
    // 设置表名
    protected $name = 'orders';
    
    // 设置主键
    protected $pk = 'id';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = 'int';
    
    // 字段类型转换
    protected $type = [
        'id' => 'integer',
        'uid' => 'integer',
        'pay_time' => 'integer',
        'create_time' => 'integer',
        'update_time' => 'integer',
        'amount' => 'float',
        'promo_value' => 'float',
        'invoiceid' => 'integer',
        'delete_time' => 'integer'
    ];
    
    // 允许批量赋值的字段
    protected $field = [
        'id',
        'uid',
        'ordernum',
        'status',
        'pay_time',
        'create_time',
        'update_time',
        'amount',
        'payment',
        'promo_code',
        'promo_type',
        'promo_value',
        'invoiceid',
        'delete_time',
        'notes'
    ];
    //关联客户
    public function client()
    {
        return $this->belongsTo(Clients::class, 'uid', 'id')->field('id,username,email');
    }
    //关联账单
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoiceid', 'id')->with('item');
    }
    
    //关联产品
    public function product()
    {
        return $this->belongsTo(Product::class, 'pid', 'id');
    }
    public function addOrExit($data = null)
    {
       
        
        $data['ordernum'] = 'GLC-' . date('YmdHis') . '-' . rand(1000, 9999);
        $data['status'] = 'Pending';
        $data['pay_time'] = time();
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['amount'] = $data['amount'] ?? 0.00;
        $data['payment'] = $data['payment'] ?? 'monthly';
        $data['promo_code'] = '';
        $data['promo_type'] = '';
        $data['promo_value'] = 0.00;
        $data['invoiceid'] = 0;
        $data['delete_time'] = 0;
        $data['notes'] = '';
        
        $result = new self();
        $result->save($data);
        return $result;
    }
}
