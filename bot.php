<?php

require_once('config.php'); 


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


$url = $addresse_fcp.$start_page;

$buffer_file = 'local.html';

$bot = new bot();
$bot->getDistantFile($url, $buffer_file);
echo 'title: '.$bot->extractTitle();

//echo $bot->buffer_contents;


class bot {
	
	var $buffer_contents;
	
	function getDistantFile ($url, $dest)
	{
		global $timeout, $wget_dir;
		
		exec($wget_dir."wget.exe --timeout=$timeout $url -O $dest");
		$this->buffer_contents = $this->getFileContents($dest);
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
		if ( preg_match_all('/<title>(.*?)<\/title>/s', $this->buffer_contents, $title) ) {
			return $title[1][0];
		}
	}

	function extractidentifier_url ()
	{
		if ( preg_match_all('/<META NAME=\"identifier-url\" CONTENT=\"(.*)\">/s/i', $this->buffer_contents, $identifier_url) ) {
			return $identifier_url[1][0];
		}
	}

	function extractrevisit_after ()
	{
		if ( preg_match_all('/<META NAME=\"revisit-after\" CONTENT=\"(.*)\">/s/i', $this->buffer_contents, $revisit_after) ) {
			return $revisit_after[1][0];
		}
	}

	function extractdescription ()
	{
		if ( preg_match_all('/<META NAME=\"description\" CONTENT=\"(.*)\">/s/i', $this->buffer_contents, $description) ) {
			return $description[1][0];
		}
	}

	function extractkeywords ()
	{
		if ( preg_match_all('/<META NAME=\"keywords\" CONTENT=\"(.*)\">/s/i', $this->buffer_contents, $keywords) ) {
			return $keywords[1][0];
		}
	}

	function extractdate_creation ()
	{
		if ( preg_match_all('/<META NAME=\"date-creation-yyyymmdd\" CONTENT=\"(.*)\">/s/i', $this->buffer_contents, $date_creation) ) {
			return $date_creation[1][0];
		}
	}

	function extractdate_revision ()
	{
		if ( preg_match_all('/<META NAME=\"date-revision-yyyymmdd\" CONTENT=\"(.*)\">/s/i', $this->buffer_contents, $date_revision) ) {
			return $date_revision[1][0];
		}
	}

	function extractcategory ()
	{
		if ( preg_match_all('/<META NAME=\"category\" CONTENT=\"(.*)\">/s/i', $this->buffer_contents, $category) ) {
			return $category[1][0];
		}
	}

	function extractpublisher ()
	{
		if ( preg_match_all('/<META NAME=\"publisher\" CONTENT=\"(.*)\">/s/i', $this->buffer_contents, $publisher) ) {
			return $publisher[1][0];
		}
	}
}
?>