<?php
set_time_limit(90);

require_once('include/config.inc.php');
require_once('class/bot.class.php');
require_once('include/database_connection.inc.php');


$buffer_file = 'local.html';
$bot = new bot($fcp_host, $fcp_port, $buffer_file);


$splitedURL['key_type'] = 'SSK';
$splitedURL['key_value'] = 'PFeLTa1si2Ml5sDeUy7eDhPso6TPdmw-2gWfQ4Jg02w,3ocfrqgUMVWA2PeorZx40TW0c-FiIOL-TWKQHoDbVdE,AQABAAE';
$splitedURL['site_name'] = 'Index';
$splitedURL['edition'] = '34';
$splitedURL['path'] = 'all.html';



$path = $bot->constructURL($splitedURL);
$bot->getDistantFile($path);

$title = $bot->extractTitle();
$metas = $bot->extractMetas();
$urls = $bot->extractURLs();
$bot->cleanURLs($urls, $splitedURL['key_type'], $splitedURL['key_value'], $splitedURL['site_name'], $splitedURL['edition']);

foreach ( $urls as $value )
{
	
	if ( !empty($value) )
	{
		$splitedURL = $bot->splitURL($value);
		echo $value;
		print_r($splitedURL);
		$id_freesite = $bot->dbGetFreesiteId($splitedURL);
		if ( $id_freesite === false )
			$id_freesite = $bot->dbAddFreesite($splitedURL);

		$bot->dbAddFreesiteURL($id_freesite, $splitedURL['path']);
		
		
		
		
	}
	
}

print_r($urls);

$insert_id = $bot->dbAddFreesite($splitedURL);
$bot->dbAddFreesiteInformations($insert_id, $title, $metas);



$url = '/USK@60I8H8HinpgZSOuTSD66AVlIFAy-xsppFr0YCzCar7c,NzdivUGCGOdlgngOGRbbKDNfSCnjI0FXjHLzJM4xkJ4,AQABAAE/ind-ex/test';
$splitedURL = $bot->splitURL($url);
print_r($splitedURL);


echo "\r\nDarknetSpiderBot is closing...\r\n";
?>