<?php
/**
 *
 * @authors Your Name (you@example.org)
 * @date    2018-07-06 09:22:08
 * @version $Id$
 */
class Debug
{
    public static function register()
    {
        ini_set('display_errors','1');
        // 显示所有错误
        error_reporting(E_ALL);
        // 异常处理
        set_exception_handler(array(__CLASS__, 'exception'));
        // 设置错误处理
        set_error_handler(array(__CLASS__, 'error'));
        // 设置关闭处理
        register_shutdown_function(array(__CLASS__, 'fatal'));
    }
    /**
     * [exception 异常处理]
     * @param  [type] $e [description]
     * @return [type]    [description]
     */
    public static function exception($e)
    {
        die(PHP_EOL . "\033[;36m " . $e->getMessage() . "\x1B[0m\n" . PHP_EOL);
        // 记录错误到日志
        self::log($e['type'] . ': ' . $e['message'], 'error');
    }
    /**
     * 错误处理
     * @param  [type] $errno      [错误号码]
     * @param  [type] $errstr     [错误信息]
     * @param  [type] $errfile    [错误文件]
     * @param  [type] $errline    [错误行]
     * @param  [type] $errcontext [description]
     * @return [type]             [description]
     */
    public static function error($errno, $errstr, $errfile, $errline)
    {
        // 使用@屏蔽错误时继续运行
        if (error_reporting() === 0) {
            return null;
        }
        die("\033[;36m " . $errstr . "\n" . ' file:' . $errfile . "\n" . ' line:' . $errline . "\x1B[0m\n" . PHP_EOL);
        //记录错误到日志
        self::log(self::errorType($errno) . ': ' . $errstr, 'error');
    }
    /**
     * 系统运行结束
     * @return [type] [description]
     */
    public static function fatal()
    {
        // 如果有错误发生时
        if (($e = error_get_last())) {
            self::error($e['type'], $e['message'], $e['file'], $e['line']);
        }
    }
    /**
     * 通过错误码获得错误类型
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public static function errorType($type)
    {
        $error = array(
            0     => 'ERROR',
            1     => 'E_ERROR',
            2     => 'E_WARNING',
            4     => 'E_PARSE',
            8     => 'E_NOTICE',
            16    => 'E_CORE_ERROR',
            32    => 'E_CORE_WARNING',
            64    => 'E_COMPILE_ERROR',
            128   => 'E_COMPILE_WARNING',
            256   => 'E_USER_ERROR',
            512   => 'E_USER_WARNING',
            1024  => 'E_USER_NOTICE',
            2408  => 'E_STRICT',
            4096  => 'E_RECOVERABLE_ERROR',
            8192  => 'E_DEPRECATED',
            16384 => 'E_USER_DEPRECATED',
            30719 => 'E_ALL',
        );
        return isset($error[$type]) ? $error[$type] : '';
    }
    /**
     * 获取错误文件内容
     * @param  [type] $e [description]
     * @return [type]    [description]
     */
    public static function getSourceCode($e)
    {
        // 获取错误页面文件内容
        $file = @file($e['file']);
        if (!$file) {
            return array('first' => 1, 'source' => '');
        }
        // 提取错误内容上下文 各10行
        $first  = ($e['line'] - 9) > 0 ? $e['line'] - 9 : 1;
        $source = array_slice($file, $first - 1, 20);
        return array('first' => $first, 'source' => $source);
    }
    public static function log($msg, $level = 'error')
    {
        $file = __DIR__ . '/log/' . date('Y-m-d') . '.log';
        is_dir(__DIR__ . '/log') or mkdir(__DIR__ . '/log', 0755, true);
        error_log($level . ': ' . $msg . PHP_EOL, 3, $file);
    }
}
