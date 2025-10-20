<?php
namespace addons\zhaomu_cloud\model;

use think\Model;

/**
 * 主机模型
 * Class Host
 * @package addons\zhaomu_cloud\model
 */
class Host extends Model
{
    // 设置表名
    protected $name = 'host';
    
    // 设置主键
    protected $pk = 'id';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
    
    // 字段类型转换
    protected $type = [
        'id' => 'integer',
        'uid' => 'integer',
        'orderid' => 'integer',
        'productid' => 'integer',
        'serverid' => 'integer',
        'regdate' => 'integer',
        'firstpaymentamount' => 'float',
        'amount' => 'float',
        'last_settle' => 'integer',
        'nextduedate' => 'integer',
        'nextinvoicedate' => 'integer',
        'termination_date' => 'integer',
        'completed_date' => 'integer',
        'promoid' => 'integer',
        'overideautosuspend' => 'boolean',
        'overidesuspenduntil' => 'integer',
        'diskusage' => 'integer',
        'disklimit' => 'integer',
        'bwusage' => 'float',
        'bwlimit' => 'integer',
        'user_cate_id' => 'integer',
        'lastupdate' => 'integer',
        'create_time' => 'integer',
        'update_time' => 'integer',
        'suspend_time' => 'integer',
        'auto_terminate_end_cycle' => 'boolean',
        'dcimid' => 'integer',
        'dcim_os' => 'integer',
        'port' => 'integer',
        'dcim_area' => 'integer',
        'flag' => 'integer',
        'initiative_renew' => 'integer',
        'agent_client' => 'boolean',
        'percent_value' => 'float'
    ];
    
    // 允许批量赋值的字段
    protected $field = [
        'id',
        'uid',
        'orderid',
        'productid',
        'serverid',
        'regdate',
        'domain',
        'payment',
        'firstpaymentamount',
        'amount',
        'billingcycle',
        'last_settle',
        'nextduedate',
        'nextinvoicedate',
        'termination_date',
        'completed_date',
        'domainstatus',
        'username',
        'password',
        'notes',
        'subscriptionid',
        'promoid',
        'suspendreason',
        'overideautosuspend',
        'overidesuspenduntil',
        'dedicatedip',
        'assignedips',
        'ns1',
        'ns2',
        'diskusage',
        'disklimit',
        'bwusage',
        'bwlimit',
        'user_cate_id',
        'lastupdate',
        'create_time',
        'update_time',
        'suspend_time',
        'auto_terminate_end_cycle',
        'auto_terminate_reason',
        'dcimid',
        'dcim_os',
        'os',
        'os_url',
        'reinstall_info',
        'remark',
        'show_last_act_message',
        'port',
        'dcim_area',
        'flag',
        'flag_cycle',
        'stream_info',
        'initiative_renew',
        'agent_client',
        'percent_value',
        'upstream_cost'
    ];
    //关联产品
    public function product()
    {
        return $this->belongsTo(Product::class, 'productid', 'id');
    }
    
    //关联自定义字段值
    public function customFieldValues()
    {
        return $this->hasMany(CustomFieldValue::class, 'relid', 'id');
    }
    public function addOrExit($data = null)
    {
        
        
        $data['regdate'] = time();
        $data['domain'] = $data['domain'] ?? 'ser' . rand(100000000000, 999999999999);
        $data['payment'] = $data['payment'] ?? 'monthly';
        $data['firstpaymentamount'] = $data['firstpaymentamount'] ?? 0.00;
        $data['amount'] = $data['amount'] ?? 0.00;
        $data['billingcycle'] = $data['billingcycle'] ?? 'monthly';
        $data['last_settle'] = 0;
        
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
        
        $data['nextduedate'] = $data['nextduedate'] ?? $nextDueTime;
        $data['nextinvoicedate'] = $data['nextinvoicedate'] ?? $nextDueTime;
        $data['termination_date'] = 0;
        $data['completed_date'] = 0;
        $data['domainstatus'] = 'Pending';
        $data['username'] = '';
        $data['password'] = '';
        $data['notes'] = '';
        $data['subscriptionid'] = '';
        $data['promoid'] = 0;
        $data['suspendreason'] = '';
        $data['overideautosuspend'] = 0;
        $data['overidesuspenduntil'] = 0;
        $data['dedicatedip'] = '';
        $data['assignedips'] = '';
        $data['ns1'] = '';
        $data['ns2'] = '';
        $data['diskusage'] = 0;
        $data['disklimit'] = 0;
        $data['bwusage'] = 0.00;
        $data['bwlimit'] = 0;
        $data['user_cate_id'] = 0;
        $data['lastupdate'] = 0;
        $data['create_time'] = time();
        $data['update_time'] = 0;
        $data['suspend_time'] = 0;
        $data['auto_terminate_end_cycle'] = 0;
        $data['auto_terminate_reason'] = '';
        $data['dcimid'] = 0;
        $data['dcim_os'] = 0;
        $data['os'] = '';
        $data['os_url'] = '';
        $data['reinstall_info'] = '';
        $data['remark'] = '';
        $data['show_last_act_message'] = 1;
        $data['port'] = 0;
        $data['dcim_area'] = 0;
        $data['flag'] = 0;
        $data['flag_cycle'] = 'monthly';
        $data['stream_info'] = '';
        $data['initiative_renew'] = 0;
        $data['agent_client'] = 0;
        $data['percent_value'] = 120.00;
        $data['upstream_cost'] = '';
        
        $result = new self();
        $result->save($data);
        return $result;
    }
}
