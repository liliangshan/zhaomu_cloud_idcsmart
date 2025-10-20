<?php

namespace addons\zhaomu_cloud\model;

use think\Model;

/**
 * 朝暮云缓存模型
 * Class HlwidcCache
 * @package addons\zhaomu_cloud\model
 */
class HlwidcCache extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'hlwidc_com_cache';
    
    // 设置主键
    protected $pk = 'set_key';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;
    
    // 字段类型转换
    protected $type = [
        'set_key'   => 'string',
        'set_value' => 'string',
    ];
    
    /**
     * 设置缓存
     * @param string $key 缓存键
     * @param mixed $value 缓存值
     * @param int $expire 过期时间（秒），0表示永不过期
     * @return bool
     */
    public static function setValue($key, $value, $expire = 0)
    {
        try {
            $setValue = is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE);
            
            // 如果有过期时间，将过期时间编码到值中
            if ($expire > 0) {
                $expireTime = time() + $expire;
                $setValue = json_encode([
                    'value' => $value,
                    'expire' => $expireTime
                ], JSON_UNESCAPED_UNICODE);
            }
            $result = self::where('set_key', $key)->find();
            if(!$result){
                $result = new self();
                $result->set_key = $key;
            }
            // 使用原生SQL避免模型问题
            $result->set_value = $setValue;
           
            
            return $result->save() !== false;
        } catch (\Exception $e) {
            // 如果数据库操作失败，返回false
            return false;
        }
    }
    
    /**
     * 获取缓存
     * @param string $key 缓存键
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function value($key, $default = null)
    {
        try {
            // 使用原生SQL查询避免模型问题
            $result = self::where('set_key', $key)->find();
            
            if (empty($result)) {
                return $default;
            }
            
            $value = $result['set_value'];
            
            // 尝试解析JSON
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // 检查是否有过期时间
                if (isset($decoded['expire'])) {
                    if (time() > $decoded['expire']) {
                        // 已过期，删除缓存
                        self::where('set_key', $key)->delete();
                        return $default;
                    }
                    return $decoded['value'];
                }
                return $decoded;
            }
            
            return $value;
        } catch (\Exception $e) {
            // 如果数据库操作失败，返回默认值
            return $default;
        } catch (\Error $e) {
            // 捕获致命错误
            return $default;
        }
    }
    
}
