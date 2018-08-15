<?php
function c($n = '', $v = 'null', $default = '')
{
    static $c = array();

    if (is_array($n)) {
        $c = array_merge($c, $n);
        return true;
    }
    $n   = explode('.', $n);
    $tmp = &$c;
    // 设置
    if ($v !== 'null') {
        foreach ($n as $val) {
            if (!isset($tmp[$val])) {
                $tmp[$val] = array();
            }
            $tmp = &$tmp[$val];
        }
        $tmp = $v;
        return true;
    }
    // 获取
    foreach ($n as $val) {

        if (isset($tmp[$val])) {
            $tmp = &$tmp[$val];
        } else {
            return $default;
        }
    }
    return $tmp;
}
function p($str)
{
    print_r($str);
    echo "\n";
}

function sha1_hmac($source, $key)
{
    return base64_encode(hash_hmac('sha1', $source, $key, true));
}
function getSignatureMethod()
{
    return 'HMAC-SHA1';
}
function getSignatureVersion()
{
    return '1.0';
}
/**
 * 记录id
 * @Author   Sean
 * @DateTime 2018-07-23
 * @param    [type]     $name  [记录名称]
 * @param    string     $value [id 如果是null为删除]
 * @return   [type]            [description]
 */
function recordId($name, $value = '')
{
    static $record = array();
    if (!$record && is_file(__DIR__ . '/recordId.php')) {
        $record = (array)require __DIR__ . '/recordId.php';
    }
    if ($value === '') {
        if (isset($record[$name])) {
            return $record[$name];
        }
        return '';
    }
    if (is_null($value)) {
        unset($record[$name]);
    }
    // 添加
    if ($value) {
        $record[$name] = $value;
    }
    return file_put_contents(__DIR__ . '/recordId.php', "<?php\nreturn " . var_export($record, true) . ';');
}