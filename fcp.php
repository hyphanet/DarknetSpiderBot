<?php
//require_once('include/config.inc.php'); 

$timeout = "20";
$fcp_host = "127.0.0.1";
$fcp_port = '9481';

$addresse = "SSK@PFeLTa1si2Ml5sDeUy7eDhPso6TPdmw-2gWfQ4Jg02w,3ocfrqgUMVWA2PeorZx40TW0c-FiIOL-TWKQHoDbVdE,AQABAAE/Index-21/";


$fp = fsockopen($fcp_host,$fcp_port,$errno, $errstr, $timeout);
if (!$fp) {
	echo "$errstr ($errno)<br>\n";
}
else
{
	$out = "ClientHello\r\n";
	$out .= "Name=DarnketSpiderBot\r\n";
	$out .= "ExpectedVersion=2.0\r\n";
	$out .= "EndMessage\r\n";	
	
	fwrite($fp, $out);

	while ( !feof($fp) )
	{  
		$buffer = fgets($fp);
		//echo $buffer;
		if (preg_match_all('/NodeHello/si', $buffer, $match)) { 
			echo "ok connection\n";
			break; 
		}
		elseif (preg_match_all('/ProtocolError/si', $buffer, $match)) { 
			echo "non ok connection\n";
			break; 
		}
	}



	$out = "ClientGet\r\n";
	$out .= "URI=$addresse\r\n";
	$out .= "Identifier=Request Number One\r\n";
	$out .= "ReturnType=direct\r\n";
	$out .= "Verbosity=1\r\n";
	$out .= "EndMessage\r\n";
	
	fwrite($fp, $out);
	
	$stop = 0;
	
	while ( !feof($fp) )
	{  
		$buffer = fgets($fp);
		//echo $buffer;

		if (preg_match_all('/ExtraDescription/si', $buffer, $match)) { 
			$ExtraDescription = explode("=",$buffer);
			$ExtraDescription = $ExtraDescription[1];
			echo "echec recup cle : $ExtraDescription";
			break;
		}
		if (preg_match_all('/Metadata.ContentType=/si', $buffer, $match)) { 
			$content_type = explode("=",$buffer);
			$content_type = $content_type[1];
			
			if (preg_match_all('/text\/html/si', $content_type, $match)) { 
				echo "ok html";
				break; 
			}
			else {
				echo "non ok html";
				break;
			}
		}
		
		if (preg_match_all('/<html>/si', $buffer, $match)) { 
			//echo "ok";
			break; 
		}

	}
	
	fclose($fp);

}
?>