<?php
require __DIR__ . '/functions.php';
c(require __DIR__ . '/config.php');
// 错误控制
require __DIR__ . '/Debug.php';
Debug::register();

$_SERVER = array (
  'TERM_PROGRAM' => 'Apple_Terminal',
  'CERTBOT_VALIDATION' => '86xMJMoWwx5W3SjayIsqAqHD98dJptu9CuttUWUe3NM',
  'RENEWED_DOMAINS' => '*.test.pjcy.cn *.test1.pjcy.cn *.test2.pjcy.cn',
  'SHELL' => '/bin/sh',
  'TERM' => 'xterm-256color',
  'Apple_PubSub_Socket_Render' => '/private/tmp/com.apple.launchd.CjY6zENhe3/Render',
  'TERM_PROGRAM_VERSION' => '388.1',
  'TERM_SESSION_ID' => 'FDDF3802-04A9-4B0F-90D6-6E8D4E52AABD',
  'USER' => 'Sean',
  'SSH_AUTH_SOCK' => '/private/tmp/com.apple.launchd.jD6OonJnMU/Listeners',
  '__CF_USER_TEXT_ENCODING' => '0x0:25:52',
  'COLUMNS' => '100',
  'PATH' => '/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin:/opt/X11/bin:/Library/Frameworks/Mono.framework/Versions/Current/Commands:/Applications/XAMPP/bin',
  '_' => '/usr/local/bin/php',
  'PWD' => '/Users/Sean/www/dnsalter',
  'LANG' => 'zh_CN.UTF-8',
  'CERTBOT_AUTH_OUTPUT' => '',
  'XPC_FLAGS' => '0x0',
  'CERTBOT_DOMAIN' => 'test2.pjcy.cn',
  'LINES' => '34',
  'XPC_SERVICE_NAME' => '0',
  'SHLVL' => '5',
  'HOME' => '/var/root',
  'LOGNAME' => 'Sean',
  'RENEWED_LINEAGE' => '/etc/letsencrypt/live/test2.pjcy.cn',
  'DISPLAY' => '/private/tmp/com.apple.launchd.CVHejbLbji/org.macosforge.xquartz:0',
  'SECURITYSESSIONID' => '186a6',
  'PHP_SELF' => '/Users/Sean/www/dnsalter/index.php',
  'SCRIPT_NAME' => '/Users/Sean/www/dnsalter/index.php',
  'SCRIPT_FILENAME' => '/Users/Sean/www/dnsalter/index.php',
  'PATH_TRANSLATED' => '/Users/Sean/www/dnsalter/index.php',
  'DOCUMENT_ROOT' => '',
  'REQUEST_TIME_FLOAT' => 1534326498.36845493316650390625,
  'REQUEST_TIME' => 1534326498,
  'argv' => 
  array (
    0 => '/Users/Sean/www/dnsalter/index.php',
    1 => 'deploy',
  ),
  'argc' => 2,
);
// 获取域名和txt记录
if(!isset($_SERVER['CERTBOT_DOMAIN'])) {
    exit('There are not certbot_domain'."\n");
} else {
    c('certbot_domain', $_SERVER['CERTBOT_DOMAIN']);
    c('certbot_validation', isset($_SERVER['CERTBOT_VALIDATION'])?$_SERVER['CERTBOT_VALIDATION']:'');
}
require __DIR__ . '/Request.php';
require __DIR__ . '/dns/DnsAlterInterface.php';
require __DIR__ . '/dnsalter.php';


$dns = new DnsAlter();
// 选择操作方式
$action = array_pop($argv);
// 可选列表
$actionArr = array(
    'add'   =>  'addRecord',
    'delete'=>  'deleteRecord',
    'deploy'=>  'deploy'
);
if (!isset($actionArr[$action]))exit('please select add or delete!');
// 添加解析
$func = $actionArr[$action];
$dns->$func();