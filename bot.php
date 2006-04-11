<?php

require_once('config.php'); 

//$url = $addresse_fcp.$start_page;
//$url = 'http://www.lemonde.fr/';
$sitepath = "/SSK@PFeLTa1si2Ml5sDeUy7eDhPso6TPdmw-2gWfQ4Jg02w,3ocfrqgUMVWA2PeorZx40TW0c-FiIOL-TWKQHoDbVdE,AQABAAE/Index-21/all.html";

$buffer_file = 'local.html';

$bot = new bot();
$bot->getDistantFile($buffer_file, $fcp, $sitepath);
echo 'title: '.$bot->extractTitle();

$urls = $bot->extractURLs();
$bot->reconstructURLs($urls, $sitepath);

print_r($urls);
//echo $bot->buffer_contents;


class bot {
	
	var $buffer_contents;
	
	function getDistantFile ($buffer_file, $fcp, $sitepath='')
	{
		global $timeout, $wget_dir;
		
		exec($wget_dir."wget.exe --timeout=$timeout ${fcp}$sitepath -O $buffer_file");
		$this->buffer_contents = $this->getFileContents($buffer_file);
	}
	
	function getFileContents ($file)
	{
		
		$handle = fopen($file, 'r') or die('Erreur à l\'ouverture du fichier'.$file);
		$contents = fread($handle, filesize ($file));
		fclose($handle);
		
		return $contents;
	}
	
	function extractTitle ()
	{
		if ( preg_match_all('/<title>(.+?)<\/title>/s', $this->buffer_contents, $title) ) {
			return $title[1][0];
		}
	}
	
	function extractMetas ()
	{
		if (preg_match_all('/<meta(.+?)>/si', $this->buffer_contents, $matches))
		{
			foreach ($matches[1] as $value) // contenu de chaque balise meta
			{
				preg_match_all('/ ?(.+?)="(.+?)" ?/si', $value, $matches2);
				foreach ($matches2[1] as $key => $value) // chaque clée
				{
					if ($value == 'name' || $value == 'content')					
						$buf[ $matches2[1][$key] ] = $matches2[2][$key];
				}
				
				if ( !empty($buf['name']) && !empty($buf['content']) )
					$meta[$buf['name']] = $buf['content'];

				unset($buf);

			}
		}
		
		return $meta;
		
	}
	
	function extractURLs ()
	{
			
	    if ( preg_match_all('/<a href="(.*?)".*>/i', $this->buffer_contents, $matches) )  
	    	return $matches[1];
	    	
	}
	
	function reconstructURLs (&$urls, $sitepath)
	{

		foreach ($urls as $key => $value)
		{
				
			if ( substr($value, 0, 7) == 'http://') // si l'url commence par http://, on la retire
				$value = '';
				
			if ( substr($value, -1) == '/') // si l'url fini par un slash, on le retire
				$value = substr($value, 0, -1);

			if ( substr($value, 0, 1) != '/') // si ce n'est pas une url absolue alors
			{
				if ( substr($value, 0, 2) == './') // on enlève éventuellement ./
					$value = substr($value, 2);
				
				// on ajoute $sitepath
				$value = $sitepath.'/'.$value;
			}
			
			// mise à jour de l'url
			$urls[$key] = $value; 
		
		}
	}
}


/*
$addresse_complete = "$addresse_fcp" . "$start_page";

exec("c:\wget\wget.exe --timeout=$timeout $addresse_complete -O c:\serveur\www\freenetbot\local.html");




$fich='local.html';
$ouvre=fopen($fich,'r');
$filesize = filesize("local.html");


while(!feof($ouvre))
{
	$ligne=fgets($ouvre,$filesize);
	
	if (eregi("<title>(.*)</title>", $ligne, $titre) == TRUE) {
		//echo $titre[1];
	}
	
	if (eregi("<a(.*)>(.*)</a>", $ligne, $liens) == TRUE) {
		$liens_complet = $liens[0];
		$test = explode("href=",$liens_complet);
		$testa = $test[1];
		$test1 = explode("\"",$testa);
		$testb = $test1[1];
		
		if (eregi("newbookmark",$testb) == TRUE) { }
		elseif (eregi("@",$testb) == TRUE) {
			$cible = "$addresse_fcp" . "$testb";
			echo "externe : $cible<br>";
		}
		else { 
			$cible = "$addresse_complete" . "$testb";
			echo "interne : $cible<br>"; 
		}
		//exit();
	}
    break;
}

fclose($ouvre);
*/

?>