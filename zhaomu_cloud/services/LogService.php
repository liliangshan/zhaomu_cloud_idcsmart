<?php

namespace addons\zhaomu_cloud\services;

/**
 * 日志服务类
 * Class LogService
 * @package addons\zhaomu_cloud\services
 */
class LogService
{
    // 日志级别
    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';
    const LEVEL_CRITICAL = 'critical';
    
    // 日志类型
    const TYPE_WECHAT = 'wechat';
    const TYPE_MESSAGE = 'message';
    const TYPE_API = 'api';
    const TYPE_ERROR = 'error';
    const TYPE_ACCESS = 'access';
    const TYPE_EMAIL = 'email';
    
    private $logPath;
    private $maxFileSize;
    private $maxFiles;
    
    public function __construct($config = [])
    {
        // 设置日志路径
        $this->logPath = $config['log_path'] ?? $this->getDefaultLogPath();
        $this->maxFileSize = $config['max_file_size'] ?? 10 * 1024 * 1024; // 10MB
        $this->maxFiles = $config['max_files'] ?? 30; // 保留30天
        
        // 确保日志目录存在
        $this->ensureLogDirectory();
    }
    
    /**
     * 获取默认日志路径
     * @return string
     */
    private function getDefaultLogPath()
    {
        // 尝试获取 CMF_DATA 路径
        if (defined('CMF_DATA')) {
            return CMF_DATA . 'logs' . DIRECTORY_SEPARATOR . 'zhaomu_cloud';
        }
        
        // 如果 CMF_DATA 未定义，使用项目根目录
        $rootPath = dirname(dirname(dirname(dirname(__DIR__))));
        return $rootPath . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'wechat_common_notify';
    }
    
    /**
     * 确保日志目录存在
     */
    private function ensureLogDirectory()
    {
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }
    
    /**
     * 记录调试日志
     * @param string $message 日志消息
     * @param array $context 上下文数据
     * @param string $type 日志类型
     */
    public function debug($message, $context = [], $type = self::TYPE_WECHAT)
    {
        $this->log(self::LEVEL_DEBUG, $message, $context, $type);
    }
    
    /**
     * 记录信息日志
     * @param string $message 日志消息
     * @param array $context 上下文数据
     * @param string $type 日志类型
     */
    public function info($message, $context = [], $type = self::TYPE_WECHAT)
    {
        $this->log(self::LEVEL_INFO, $message, $context, $type);
    }
    
    /**
     * 记录警告日志
     * @param string $message 日志消息
     * @param array $context 上下文数据
     * @param string $type 日志类型
     */
    public function warning($message, $context = [], $type = self::TYPE_WECHAT)
    {
        $this->log(self::LEVEL_WARNING, $message, $context, $type);
    }
    
    /**
     * 记录错误日志
     * @param string $message 日志消息
     * @param array $context 上下文数据
     * @param string $type 日志类型
     */
    public function error($message, $context = [], $type = self::TYPE_ERROR)
    {
        $this->log(self::LEVEL_ERROR, $message, $context, $type);
    }
    
    /**
     * 记录严重错误日志
     * @param string $message 日志消息
     * @param array $context 上下文数据
     * @param string $type 日志类型
     */
    public function critical($message, $context = [], $type = self::TYPE_ERROR)
    {
        $this->log(self::LEVEL_CRITICAL, $message, $context, $type);
    }
    
    /**
     * 记录微信API调用日志
     * @param string $api API名称
     * @param array $request 请求数据
     * @param array $response 响应数据
     * @param int $statusCode HTTP状态码
     * @param float $duration 请求耗时（秒）
     */
    public function api($api, $request = [], $response = [], $statusCode = 200, $duration = 0)
    {
        $context = [
            'api' => $api,
            'request' => $request,
            'response' => $response,
            'status_code' => $statusCode,
            'duration' => $duration
        ];
        
        $message = "API调用: {$api}";
        if ($statusCode >= 400) {
            $this->error($message, $context, self::TYPE_API);
        } else {
            $this->info($message, $context, self::TYPE_API);
        }
    }
    
    /**
     * 记录消息处理日志
     * @param string $openid 用户openid
     * @param string $msgType 消息类型
     * @param string $content 消息内容
     * @param string $reply 回复内容
     * @param bool $success 是否成功
     */
    public function message($openid, $msgType, $content, $reply = '', $success = true)
    {
        $context = [
            'openid' => $openid,
            'msg_type' => $msgType,
            'content' => $content,
            'reply' => $reply,
            'success' => $success
        ];
        
        $message = "消息处理: {$msgType}";
        if ($success) {
            $this->info($message, $context, self::TYPE_MESSAGE);
        } else {
            $this->error($message, $context, self::TYPE_MESSAGE);
        }
    }
    
    /**
     * 记录访问日志
     * @param string $method HTTP方法
     * @param string $url 请求URL
     * @param array $params 请求参数
     * @param int $statusCode HTTP状态码
     * @param string $ip 客户端IP
     */
    public function access($method, $url, $params = [], $statusCode = 200, $ip = '')
    {
        $context = [
            'method' => $method,
            'url' => $url,
            'params' => $params,
            'status_code' => $statusCode,
            'ip' => $ip
        ];
        
        $message = "访问日志: {$method} {$url}";
        $this->info($message, $context, self::TYPE_ACCESS);
    }
    
    /**
     * 记录日志
     * @param string $level 日志级别
     * @param string $message 日志消息
     * @param array $context 上下文数据
     * @param string $type 日志类型
     */
    public function log($level, $message, $context = [], $type = self::TYPE_WECHAT)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logData = [
            'timestamp' => $timestamp,
            'level' => strtoupper($level),
            'type' => $type,
            'message' => $message,
            'context' => $context,
            'memory' => $this->formatBytes(memory_get_usage(true)),
            'pid' => getmypid()
        ];
        
        $logLine = $this->formatLogLine($logData);
        
        // 写入日志文件
        $this->writeToFile($logLine, $type);
        
        // 清理旧日志文件
        $this->cleanOldLogs($type);
    }
    
    /**
     * 格式化日志行
     * @param array $logData 日志数据
     * @return string
     */
    private function formatLogLine($logData)
    {
        $line = "[{$logData['timestamp']}] [{$logData['level']}] [{$logData['type']}] [PID:{$logData['pid']}] [MEM:{$logData['memory']}] ";
        $line .= $logData['message'];
        
        if (!empty($logData['context'])) {
            $line .= " | Context: " . json_encode($logData['context'], JSON_UNESCAPED_UNICODE);
        }
        
        $line .= PHP_EOL;
        
        return $line;
    }
    
    /**
     * 写入日志文件
     * @param string $logLine 日志行
     * @param string $type 日志类型
     */
    private function writeToFile($logLine, $type)
    {
        $filename = $this->getLogFilename($type);
        $filepath = $this->logPath . DIRECTORY_SEPARATOR . $filename;
        
        // 检查文件大小，如果超过限制则轮转
        if (file_exists($filepath) && filesize($filepath) > $this->maxFileSize) {
            $this->rotateLogFile($filepath);
        }
        
        // 写入日志
        file_put_contents($filepath, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * 获取日志文件名
     * @param string $type 日志类型
     * @return string
     */
    private function getLogFilename($type)
    {
        $date = date('Y-m-d');
        return "{$type}_{$date}.log";
    }
    
    /**
     * 轮转日志文件
     * @param string $filepath 文件路径
     */
    private function rotateLogFile($filepath)
    {
        $timestamp = date('Y-m-d_H-i-s');
        $rotatedFile = $filepath . '.' . $timestamp;
        rename($filepath, $rotatedFile);
    }
    
    /**
     * 清理旧日志文件
     * @param string $type 日志类型
     */
    private function cleanOldLogs($type)
    {
        $pattern = $this->logPath . DIRECTORY_SEPARATOR . "{$type}_*.log*";
        $files = glob($pattern);
        
        if (count($files) > $this->maxFiles) {
            // 按修改时间排序
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            // 删除最旧的文件
            $filesToDelete = array_slice($files, 0, count($files) - $this->maxFiles);
            foreach ($filesToDelete as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }
    
    /**
     * 格式化字节数
     * @param int $bytes 字节数
     * @return string
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * 获取日志文件列表
     * @param string $type 日志类型
     * @return array
     */
    public function getLogFiles($type = null)
    {
        $pattern = $this->logPath . DIRECTORY_SEPARATOR . ($type ? "{$type}_*.log*" : "*.log*");
        $files = glob($pattern);
        
        $result = [];
        foreach ($files as $file) {
            $result[] = [
                'filename' => basename($file),
                'filepath' => $file,
                'size' => filesize($file),
                'size_formatted' => $this->formatBytes(filesize($file)),
                'modified' => filemtime($file),
                'modified_formatted' => date('Y-m-d H:i:s', filemtime($file))
            ];
        }
        
        // 按修改时间倒序排序
        usort($result, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });
        
        return $result;
    }
    
    /**
     * 读取日志文件内容
     * @param string $filename 文件名
     * @param int $lines 读取行数（0表示全部）
     * @return string
     */
    public function readLogFile($filename, $lines = 0)
    {
        $filepath = $this->logPath . DIRECTORY_SEPARATOR . $filename;
        
        if (!file_exists($filepath)) {
            return '';
        }
        
        if ($lines <= 0) {
            return file_get_contents($filepath);
        }
        
        $content = '';
        $handle = fopen($filepath, 'r');
        $lineCount = 0;
        
        while (($line = fgets($handle)) !== false && $lineCount < $lines) {
            $content = $line . $content; // 倒序读取
            $lineCount++;
        }
        
        fclose($handle);
        
        return $content;
    }
    
    /**
     * 清空日志文件
     * @param string $type 日志类型
     */
    public function clearLogs($type = null)
    {
        $pattern = $this->logPath . DIRECTORY_SEPARATOR . ($type ? "{$type}_*.log*" : "*.log*");
        $files = glob($pattern);
        
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
