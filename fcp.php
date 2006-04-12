<?php
//require_once('include/config.inc.php'); 

$timeout = "20";
$fcp_host = "127.0.0.1";
$fcp_port = '9481';

$addresse = "SSK@PFeLTa1si2Ml5sDeUy7eDhPso6TPdmw-2gWfQ4Jg02w,3ocfrqgUMVWA2PeorZx40TW0c-FiIOL-TWKQHoDbVdE,AQABAAE/Index-21/";

$ok_hello = 0;
$error = NULL;

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

		if (preg_match_all('/ProtocolError/si', $buffer, $match)) { 
			$error = "non ok connection\n";
			break; 
		}
	
		if (preg_match_all('/EndMessage/si', $buffer, $match)) { 
			$ok_hello = 1;
			break; 
		}
	}

	if ($ok_hello == 1) {
		$out = "ClientGet\r\n";
		$out .= "URI=$addresse\r\n";
		$out .= "Identifier=Request Number One\r\n";
		$out .= "ReturnType=NONE\r\n";
		$out .= "Verbosity=1\r\n";
		$out .= "EndMessage\r\n";
		
		fwrite($fp, $out);
		$count = 1;
		while ( !feof($fp) )
		{  
			$buffer = fgets($fp);
	
			if (preg_match_all('/ExtraDescription/si', $buffer, $match)) { 
				$ExtraDescription = explode("=",$buffer);
				$ExtraDescription = $ExtraDescription[1];
				$error = "Get Failed : $ExtraDescription";
				break;
			}
			if (preg_match_all('/Metadata.ContentType=/si', $buffer, $match)) { 
				$content_type = explode("=",$buffer);
				$content_type = $content_type[1];
				
				if (preg_match_all('/text\/html/si', $content_type, $match)) { 	}
				else {
					$error = "Error Content Type";
					break;
				}
			}
	
			if (preg_match_all('/GetFailed/si', $buffer, $match)) { 
				$count = 2;
				$error = "Get Failed";
			}
					
			if (preg_match_all('/EndMessage/si', $buffer, $match)) { 
				$count = $count + 1;
				if ($count > 2) { break; }
			}
		}
	
		if ($error == NULL) {
			$out = "ClientGet\r\n";
			$out .= "URI=$addresse\r\n";
			$out .= "Identifier=Request Number One\r\n";
			$out .= "ReturnType=DISK\r\n";
			$out .= "Filename=D:\\Darknet\\bot\\test1.html\r\n";
			$out .= "Verbosity=0\r\n";
			$out .= "EndMessage\r\n";
			
			fwrite($fp, $out);
			
			while ( !feof($fp) )
			{  
				$buffer = fgets($fp);

				if (preg_match_all('/CodeDescription/si', $buffer, $match)) { 
					$CodeDescription = explode("=",$buffer);
					$CodeDescription = $CodeDescription[1];
					$error = "Get Failed : $CodeDescription";
					break;
				}

				if (preg_match_all('/EndMessage/si', $buffer, $match)) { 
					break; 
				}

				$error = "all ok";		
			}
		}
	}
	
echo $error;

fclose($fp);
}
?>