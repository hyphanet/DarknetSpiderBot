<?php

class bot {
	
	var $fcp_host;
	var $fcp_port;
	
	var $buffer;
	var $buffer_file;
	
	var $current_urls;
	
	// Constructor
	function bot ($fcp_host, $fcp_port, $buffer_file)
	{
		$this->fcp_host = $fcp_host;
		$this->fcp_port = $fcp_port;
		$this->buffer_file = $buffer_file;
	}
	
	
	// Retrive functions
	function getDistantFile ($path, $timeout=30)
	{
		
		$fp = fsockopen($this->fcp_host, $this->fcp_port, $errno, $errstr, $timeout);
		if (!$fp) {
			echo "$errstr ($errno)<br />\n";
		}
		else
		{
			$out = "GET $path HTTP/1.1\r\n";
			$out .= "Host: $fcp_host\r\n";
			$out .= "Connection: Close\r\n\r\n";
		
			fwrite($fp, $out);
			
			while ( !feof($fp) )
			{
				$buffer .= fgets($fp, 4096);
			}
			fclose($fp);
		}
		
		$this->buffer = $buffer;
	}
	
	function getFileContents ($filename)
	{
		
		$handle = fopen($filename, 'r') or die("Error during open \"$filename\"");
		$contents = fread($handle);
		fclose($handle);
		
		return $contents;
	}
	
	function getLastEdition ($path)
	{
		
		$this->getDistantFile($path, 60);
		
		if ( preg_match('/\nLocation: (.+)/', $this->buffer, $matches) )
			return $matches[1];
			
		return false;
	}
	
	
	// URLs processing functions
	function splitURL ($url)
	{
		// the URL must begin by /[freenet:]KEY@
		
		// strip freenet:
		if ( substr($url, 0, 9) == '/freenet:')
			$url = '/'.substr($url, 9);

		
		$splitedURL['key_type'] = substr($url, 1, 3);
		
		$second_slashe_pos = strpos($url, '/', 5);
		
		
		if ( $splitedURL['key_type'] == 'CHK' )
		{
			if ($second_slashe_pos != 0)
			{
				$splitedURL['key_value'] = substr($url, 5, $second_slashe_pos-5);
				$splitedURL['path'] = substr($url, $second_slashe_pos+1);
			}
			else
			{
				$splitedURL['key_value'] = substr($url, 5);
			}
		}
		else
		{
			//$path = substr($url, $second_slashe_pos+1);
			$splitedURL['key_value'] = substr($url, 5, $second_slashe_pos-5);
			
			if ( preg_match('#^([^/]+)[/-]+([0-9]+)/*(.*)$#', substr($url, $second_slashe_pos+1), $matches ) )
			{
				$splitedURL['site_name'] = $matches[1];
				$splitedURL['edition'] = $matches[2];
				$splitedURL['path'] = $matches[3];
			}
			else
			{
				$splitedURL['site_name'] = substr($url, $second_slashe_pos+1);
			}
		}
		
		if ( substr($splitedURL['path'], 0, 1) == '/' )
			$splitedURL['path'] = substr($splitedURL['path'], 1);
			
			
		return $splitedURL;
	}
	
	function constructURL ($splitedURL)
	{
		switch ($splitedURL['key_type'])
		{
			case 'USK':
				$url = '/USK@'.$splitedURL['key_value'].'/'.$splitedURL['site_name'].'/-'.$splitedURL['edition'];
				break;
				
			case 'SSK':
				$url = '/SSK@'.$splitedURL['key_value'].'/'.$splitedURL['site_name'].'-'.$splitedURL['edition'].'/'.$splitedURL['path'];
				break;
				
			case 'CHK':
				$url = '/CHK@'.$splitedURL['key_value'].'/'.$splitedURL['path'];
				break;
				
			default:
				return false;
		}
		return $url;
	
	}
	

	
	function cleanURLs (&$urls, $key_type, $key_value, $site_name, $edition)
	{
		// todo: support des ../
		
		foreach ($urls as $key => $value)
		{
			
			$value = trim($value);
			
			if ( substr($value, 0, 7) == 'http://' || substr($value, 0, 13) == '/?newbookmark' ) // si l'url commence par http://, on la retire
			{
				$value = '';
			}
			elseif ( substr($value, 0, 1) != '/') // si ce n'est pas une url absolue alors
			{
				if ( substr($value, 0, 2) == './') // on enlève éventuellement ./
					$value = substr($value, 2);
				
				// on ajoute $sitepath
				$value = '/'.$key_type.'@'.$key_value.'/'.$site_name.'-'.$edition.'/'.$value;
			}
			
			if ( substr($value, -1) == '/') // si l'url fini par un slash, on le retire
				$value = substr($value, 0, -1);
			
			// On retire les liens vers les diverses versions
			if ( preg_match("#^/[A-Z]{3,3}@$key_value/$site_name/?-[0-9]+$#i", $value, $matches, PREG_OFFSET_CAPTURE) )
				$value = '';
				
			// mise à jour de l'url
			$urls[$key] = $value; 
		
		}
	}
	
	// Extraction processing functions
	function extractTitle ()
	{
		if ( preg_match_all('/<title>(.+?)<\/title>/s', $this->buffer, $title) ) {
			return $title[1][0];
		}
	}
	
	function extractMetas ()
	{
		if (preg_match_all('/<meta(.+?)>/si', $this->buffer, $matches))
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
					$metas[$buf['name']] = $buf['content'];

				unset($buf);

			}
		}
		
		return $metas;
		
	}
	
	function extractURLs ()
	{
			
	    if ( preg_match_all('/<a href="(.*?)".*>/i', $this->buffer, $matches) )  
	    	return $matches[1];
	    	
	}
	
	
	// Database processing
	function dbAddFreesite ($splitedURL)
	{
		if ( $splitedURL['key_type'] != 'CHK' )
			$splitedURL['key_type'] = 'SSK';
		
		mysql_query("INSERT INTO freesites_keys ( key_type, key_value, site_name, edition, created, last_update ) VALUES ('$splitedURL[key_type]', '$splitedURL[key_value]', '$splitedURL[site_name]', '$splitedURL[edition]', NOW(), NOW() ) ");
		
		return mysql_insert_id();
	}
	
	function dbGetFreesiteId ($splitedURL)
	{
		
		$result = mysql_query("SELECT id FROM freesites_keys WHERE key_value = '$splitedURL[key_value]' ");
		if ( mysql_num_rows($result) > 0 )
		{
			list($id_freesite) = mysql_fetch_row($result);
			return $id_freesite;
		}
		else
			return false;
	}
	
	function dbAddFreesiteInformations ($id_freesite, $title, $metas)
	{
		
		mysql_query("INSERT INTO freesites_informations ( id_freesite, title, meta_description, meta_keywords ) VALUES ( '$id_freesite', '$title', '$metas[description]', '$metas[keywords]' ) ");
	}
	
	function dbAddFreesiteURL ($id_freesite, $url)
	{
		
		mysql_query("INSERT INTO freesites_urls ( id_freesite, path ) VALUES ( '$id_freesite', '$url' ) ");
	}
	
	function requestingFreesite ($splitedURL)
	{
		
		$path = $this->constructURL($splitedURL);
		$this->getDistantFile($path);
		
		
	}

}

?>