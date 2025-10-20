<?php
namespace addons\zhaomu_cloud\model;

use think\Model;

/**
 * 服务器模型
 * Class Server
 * @package addons\zhaomu_cloud\model
 */
class Server extends Model
{
    // 设置表名
    protected $name = 'servers';
    
    // 设置主键
    protected $pk = 'id';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
    
    // 字段类型转换
    protected $type = [
        'id' => 'integer',
        'gid' => 'integer',
        'monthly_cost' => 'float',
        'max_accounts' => 'integer',
        'secure' => 'boolean',
        'port' => 'integer',
        'active' => 'boolean',
        'disabled' => 'boolean',
        'link_status' => 'integer'
    ];
    
    // 允许批量赋值的字段
    protected $field = [
        'id',
        'gid',
        'name',
        'ip_address',
        'assigned_ips',
        'hostname',
        'monthly_cost',
        'noc',
        'status_address',
        'name_server1',
        'name_server1_ip',
        'name_server2',
        'name_server2_ip',
        'name_server3',
        'name_server3_ip',
        'name_server4',
        'name_server4_ip',
        'name_server5',
        'name_server5_ip',
        'max_accounts',
        'username',
        'password',
        'accesshash',
        'secure',
        'port',
        'active',
        'disabled',
        'server_type',
        'link_status',
        'type'
    ];
    public function addOrExit($data=null){
        $result = $this->where('name', '朝暮数据')->find();
        if($result){
            return $result;
        }
        $data['name'] = '朝暮数据';
        $data['gid'] = (new ServerGroup())->addOrExit()->id;
        $data['monthly_cost'] = 0;
        $data['max_accounts'] = 0;
        $data['secure'] = 0;
        $data['port'] = 0;
        $data['active'] = 0;
        $data['disabled'] = 0;
        $data['link_status'] = 0;
        $data['type'] = 'zhaomucloudapi';

        $result = new self();
        $result->save($data);
        return $result;
    }
}
