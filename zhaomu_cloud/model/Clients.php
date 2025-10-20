<?php

namespace addons\zhaomu_cloud\model;

use think\Model;

/**
 * 客户模型
 * Class Clients
 * @package addons\zhaomu_cloud\model
 */
class Clients extends Model
{
    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'clients';

    /**
     * 数据表名
     * @var string
     */
    protected $table = 'shd_clients';

    /**
     * 自动时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = false;

    /**
     * 字段类型转换
     * @var array
     */
    protected $type = [
        'id' => 'integer',
        'usertype' => 'integer',
        'sex' => 'integer',
        'wechat_id' => 'integer',
        'phone_code' => 'integer',
        'currency' => 'integer',
        'credit' => 'float',
        'taxexempt' => 'boolean',
        'latefeeoveride' => 'boolean',
        'overideduenotices' => 'boolean',
        'separateinvoices' => 'boolean',
        'disableautocc' => 'boolean',
        'billingcid' => 'integer',
        'securityqid' => 'integer',
        'groupid' => 'integer',
        'gatewayid' => 'integer',
        'lastlogin' => 'integer',
        'status' => 'boolean',
        'emailoptout' => 'integer',
        'marketing_emails_opt_in' => 'boolean',
        'overrideautoclose' => 'integer',
        'allow_sso' => 'integer',
        'email_verified' => 'integer',
        'create_time' => 'integer',
        'update_time' => 'integer',
        'pwresetexpiry' => 'integer',
        'initiative_renew' => 'boolean',
        'is_login_sms_reminder' => 'integer',
        'sale_id' => 'integer',
        'activation' => 'integer',
        'second_verify' => 'boolean',
        'track_status' => 'boolean',
        'email_remind' => 'boolean',
        'is_open_credit_limit' => 'boolean',
        'credit_limit' => 'float',
        'credit_limit_balance' => 'float',
        'repayment_date' => 'integer',
        'bill_generation_date' => 'integer',
        'bill_repayment_period' => 'integer',
        'credit_limit_create_time' => 'integer',
        'api_open' => 'boolean',
        'api_create_time' => 'integer',
        'api_lock_time' => 'integer',
    ];

    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'password',
        'authdata',
        'cardnum',
        'startdate',
        'expdate',
        'issuenumber',
        'bankcode',
        'bankacct',
        'pwresetkey',
        'api_password',
    ];

    /**
     * 只读字段
     * @var array
     */
    protected $readonly = [
        'id',
        'uuid',
        'create_time',
    ];

    /**
     * 搜索器：用户名
     * @param $query
     * @param $value
     */
    public function searchUsernameAttr($query, $value)
    {
        $query->where('username', 'like', '%' . $value . '%');
    }

    /**
     * 搜索器：邮箱
     * @param $query
     * @param $value
     */
    public function searchEmailAttr($query, $value)
    {
        $query->where('email', 'like', '%' . $value . '%');
    }

    /**
     * 搜索器：手机号
     * @param $query
     * @param $value
     */
    public function searchPhonenumberAttr($query, $value)
    {
        $query->where('phonenumber', 'like', '%' . $value . '%');
    }

    /**
     * 搜索器：状态
     * @param $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        $query->where('status', $value);
    }

    /**
     * 搜索器：用户类型
     * @param $query
     * @param $value
     */
    public function searchUsertypeAttr($query, $value)
    {
        $query->where('usertype', $value);
    }

    /**
     * 获取器：格式化创建时间
     * @param $value
     * @return string
     */
    public function getCreateTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    /**
     * 获取器：格式化最后登录时间
     * @param $value
     * @return string
     */
    public function getLastloginAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    /**
     * 获取器：格式化更新时间
     * @param $value
     * @return string
     */
    public function getUpdateTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    /**
     * 获取器：状态文本
     * @param $value
     * @return string
     */
    public function getStatusTextAttr($value, $data)
    {
        $status = [
            0 => '禁用',
            1 => '正常',
        ];
        return $status[$data['status']] ?? '未知';
    }

    /**
     * 获取器：用户类型文本
     * @param $value
     * @return string
     */
    public function getUsertypeTextAttr($value, $data)
    {
        $types = [
            1 => '个人用户',
            2 => '企业用户',
        ];
        return $types[$data['usertype']] ?? '未知';
    }

    /**
     * 获取器：性别文本
     * @param $value
     * @return string
     */
    public function getSexTextAttr($value, $data)
    {
        $sex = [
            0 => '未知',
            1 => '男',
            2 => '女',
        ];
        return $sex[$data['sex']] ?? '未知';
    }

    /**
     * 修改器：设置密码
     * @param $value
     * @return string
     */
    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * 修改器：设置创建时间
     * @param $value
     * @return int
     */
    public function setCreateTimeAttr($value)
    {
        return $value ?: time();
    }

    /**
     * 修改器：设置更新时间
     * @param $value
     * @return int
     */
    public function setUpdateTimeAttr($value)
    {
        return time();
    }

    /**
     * 关联：客户组
     * @return \think\model\relation\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo('ClientGroups', 'groupid', 'id');
    }

    /**
     * 关联：销售员
     * @return \think\model\relation\BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo('Sales', 'sale_id', 'id');
    }

    /**
     * 关联：货币
     * @return \think\model\relation\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo('Currencies', 'currency', 'id');
    }

    /**
     * 关联：网关
     * @return \think\model\relation\BelongsTo
     */
    public function gateway()
    {
        return $this->belongsTo('Gateways', 'gatewayid', 'id');
    }

    /**
     * 关联：个人认证（审核通过）
     * @return \think\model\relation\HasOne
     */
    public function personCertification()
    {
        return $this->hasOne('CertifiPerson', 'auth_user_id', 'id')->where('status', 1);
    }

    /**
     * 关联：企业认证（审核通过）
     * @return \think\model\relation\HasOne
     */
    public function companyCertification()
    {
        return $this->hasOne('CertifiCompany', 'auth_user_id', 'id')->where('status', 1);
    }

    /**
     * 关联：所有个人认证记录
     * @return \think\model\relation\HasMany
     */
    public function personCertifications()
    {
        return $this->hasMany('CertifiPerson', 'auth_user_id', 'id');
    }

    /**
     * 关联：所有企业认证记录
     * @return \think\model\relation\HasMany
     */
    public function companyCertifications()
    {
        return $this->hasMany('CertifiCompany', 'auth_user_id', 'id');
    }

    /**
     * 验证密码
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password)
    {
        return password_verify($password, $this->password);
    }

    /**
     * 检查用户状态
     * @return bool
     */
    public function isActive()
    {
        return $this->status == 1;
    }

    /**
     * 检查邮箱是否已验证
     * @return bool
     */
    public function isEmailVerified()
    {
        return $this->email_verified == 1;
    }

    /**
     * 获取完整手机号
     * @return string
     */
    public function getFullPhone()
    {
        return '+' . $this->phone_code . $this->phonenumber;
    }

    /**
     * 获取完整地址
     * @return string
     */
    public function getFullAddress()
    {
        $address = [];
        if ($this->country) $address[] = $this->country;
        if ($this->province) $address[] = $this->province;
        if ($this->city) $address[] = $this->city;
        if ($this->region) $address[] = $this->region;
        if ($this->address1) $address[] = $this->address1;
        
        return implode(' ', $address);
    }

    /**
     * 更新最后登录信息
     * @param string $ip
     * @param string $host
     * @return bool
     */
    public function updateLastLogin($ip = '', $host = '')
    {
        $this->lastlogin = time();
        $this->lastloginip = $ip;
        $this->ip = $ip;
        $this->host = $host;
        
        return $this->save();
    }

    /**
     * 根据用户名查找客户
     * @param string $username
     * @return Clients|null
     */
    public static function findByUsername($username)
    {
        return self::where('username', $username)->find();
    }

    /**
     * 根据邮箱查找客户
     * @param string $email
     * @return Clients|null
     */
    public static function findByEmail($email)
    {
        return self::where('email', $email)->find();
    }

    /**
     * 根据手机号查找客户
     * @param string $phone
     * @return Clients|null
     */
    public static function findByPhone($phone)
    {
        return self::where('phonenumber', $phone)->find();
    }

    /**
     * 根据UUID查找客户
     * @param string $uuid
     * @return Clients|null
     */
    public static function findByUuid($uuid)
    {
        return self::where('uuid', $uuid)->find();
    }
}
