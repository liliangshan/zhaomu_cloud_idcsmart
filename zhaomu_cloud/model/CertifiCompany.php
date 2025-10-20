<?php
namespace addons\zhaomu_cloud\model;

use think\Model;

/**
 * 企业认证模型
 * Class CertifiCompany
 * @package addons\zhaomu_cloud\model
 */
class CertifiCompany extends Model
{
    // 设置表名
    protected $name = 'certifi_company';
    
    // 设置主键
    protected $pk = 'id';
    
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    
    // 字段类型转换
    protected $type = [
        'id' => 'integer',
        'auth_user_id' => 'integer',
        'auth_card_type' => 'integer',
        'status' => 'integer',
        'create_time' => 'integer',
        'update_time' => 'integer',
    ];
    
    // 字段过滤
    protected $field = [
        'id',
        'auth_user_id',
        'auth_real_name',
        'auth_card_type',
        'auth_card_number',
        'company_name',
        'company_organ_code',
        'img_one',
        'img_two',
        'img_three',
        'img_four',
        'status',
        'certify_id',
        'auth_fail',
        'create_time',
        'update_time',
        'bank',
        'phone',
        'custom_fields1',
        'custom_fields2',
        'custom_fields3',
        'custom_fields4',
        'custom_fields5',
        'custom_fields6',
        'custom_fields7',
        'custom_fields8',
        'custom_fields9',
        'custom_fields10',
    ];
    
    /**
     * 获取认证状态文本
     * @param $value
     * @param $data
     * @return string
     */
    public function getStatusTextAttr($value, $data)
    {
        $statusMap = [
            0 => '待审核',
            1 => '审核通过',
            2 => '审核失败',
        ];
        return $statusMap[$data['status']] ?? '未知状态';
    }
    
    /**
     * 获取证件类型文本
     * @param $value
     * @param $data
     * @return string
     */
    public function getCardTypeTextAttr($value, $data)
    {
        $typeMap = [
            0 => '身份证',
            1 => '护照',
            2 => '港澳通行证',
            3 => '台胞证',
        ];
        return $typeMap[$data['auth_card_type']] ?? '未知类型';
    }
    
    /**
     * 根据用户ID获取认证信息
     * @param int $userId
     * @return array|null
     */
    public function getCertificationByUserId($userId)
    {
        return $this->where('auth_user_id', $userId)->find();
    }
    
    /**
     * 创建认证记录
     * @param array $data
     * @return bool
     */
    public function createCertification($data)
    {
        return $this->save($data);
    }
    
    /**
     * 更新认证状态
     * @param int $id
     * @param int $status
     * @param string $failReason
     * @return bool
     */
    public function updateStatus($id, $status, $failReason = '')
    {
        $updateData = [
            'status' => $status,
            'update_time' => time(),
        ];
        
        if ($status == 2 && $failReason) {
            $updateData['auth_fail'] = $failReason;
        }
        
        return $this->where('id', $id)->update($updateData);
    }
    
    /**
     * 获取企业认证列表
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getCompanyList($where = [], $page = 1, $limit = 20)
    {
        $query = $this->where($where);
        
        $total = $query->count();
        $list = $query->page($page, $limit)->select();
        
        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }
}
