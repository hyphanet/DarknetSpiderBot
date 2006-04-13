<?php
set_time_limit(90);

require_once('include/config.inc.php');
require_once('class/bot.class.php');
require_once('database_connection.inc.php');


$buffer_file = 'local.html';
$bot = new bot($fcp_host, $fcp_port, $buffer_file);

$splitedURL['key_type'] = 'SSK';
$splitedURL['key_value'] = 'PFeLTa1si2Ml5sDeUy7eDhPso6TPdmw-2gWfQ4Jg02w,3ocfrqgUMVWA2PeorZx40TW0c-FiIOL-TWKQHoDbVdE,AQABAAE';
$splitedURL['site_name'] = 'Index';
$splitedURL['edition'] = '21';


$path = $bot->constructURL($splitedURL);
$bot->getDistantFile($path);

echo $bot->extractTitle();

$bot->dbAddFreesite($splitedURL);

echo "\r\nDarknetSpiderBot is closing...\r\n";
?>