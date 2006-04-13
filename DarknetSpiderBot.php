<?php
set_time_limit(90);

require_once('include/config.inc.php');
require_once('class/bot.class.php');



$buffer_file = 'local.html';

$key_value = 'PFeLTa1si2Ml5sDeUy7eDhPso6TPdmw-2gWfQ4Jg02w,3ocfrqgUMVWA2PeorZx40TW0c-FiIOL-TWKQHoDbVdE,AQABAAE';
$site_name = 'Index';
$last_know_edition = '20';



$bot = new bot($fcp_host, $fcp_port, $buffer_file);

//$url = $bot->getLastEdition($key_value, $site_name, $last_know_edition);
//print_r($bot->splitURL($url.'/test/index.html'));


$sitepath = "/USK@$sitekey/$sitename";
$bot->getDistantFile($fcp_host, $fcp_port, $sitepath.'/-1');


//$urls = $bot->extractURLs();
//$bot->cleanURLs($urls, $sitekey, $sitename);

//print_r($urls);
//echo $bot->buffer;
print_r($bot->extractTitle());

echo "\r\nDarknetSpiderBot is closing...\r\n";





?>