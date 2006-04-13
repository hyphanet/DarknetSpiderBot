<?php

class bot {
	
	var $fcp_host;
	var $fcp_port;
	
	var $buffer;
	var $buffer_file;
	
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
		$splitedURL['key_value'] = substr($url, 5, $second_slashe_pos-5);
		
		if ( $splitedURL['key_type'] == 'CHK' )
		{
			$splitedURL['path'] = substr($url, $second_slashe_pos+1);
		}
		else
		{
			preg_match('#^(.+)[/-]+([0-9]+)(.*)$#', substr($url, $second_slashe_pos+1), $matches );
			$splitedURL['site_name'] = $matches[1];
			$splitedURL['edition'] = $matches[2];
			$splitedURL['path'] = $matches[3];
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
	

	
	function cleanURLs (&$urls, $sitekey, $sitename)
	{
		// todo: support des ../
		
		foreach ($urls as $key => $value)
		{
			
			$value = trim($value);
			
			if ( substr($value, 0, 7) == 'http://') // si l'url commence par http://, on la retire
			{
				$value = '';
			}
			elseif ( substr($value, 0, 1) != '/') // si ce n'est pas une url absolue alors
			{
				if ( substr($value, 0, 2) == './') // on enlève éventuellement ./
					$value = substr($value, 2);
				
				// on ajoute $sitepath
				$value = '/'.$sitekey.'/'.$sitename.'/'.$value;
			}
			
			if ( substr($value, -1) == '/') // si l'url fini par un slash, on le retire
				$value = substr($value, 0, -1);
			
			// On retire les liens vers les diverses versions
			if ( preg_match("#^/[A-Z]{3,3}@$sitekey/$sitename/?-[0-9]+$#i", $value, $matches, PREG_OFFSET_CAPTURE) )
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
	

}

?>