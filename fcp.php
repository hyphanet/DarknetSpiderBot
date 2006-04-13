<?php
//require_once('include/config.inc.php'); 

$timeout = "20";
$fcp_host = "127.0.0.1";
$fcp_port = '9481';
$filename = "D:\\darknet\\bot\\local.html";

$addresse = "SSK@PFeLTa1si2Ml5sDeUy7eDhPso6TPdmw-2gWfQ4Jg02w,3ocfrqgUMVWA2PeorZx40TW0c-FiIOL-TWKQHoDbVdE,AQABAAE/Index-21/";

$ok_hello = 0;
$error = NULL;

//Connect to FCP
$fp = fsockopen($fcp_host,$fcp_port,$errno, $errstr, $timeout);

//Stop if connexion is impossible
if (!$fp) {
	echo "$errstr ($errno)<br>\n";
}
else
{
	//Send ClientHello
	$out = "ClientHello\r\n";
	$out .= "Name=DarnketSpiderBot\r\n";
	$out .= "ExpectedVersion=2.0\r\n";
	$out .= "EndMessage\r\n";	
	
	//Send command to buffer
	fwrite($fp, $out);
	
	//Read output from buffer
	while ( !feof($fp) )
	{  
		$buffer = fgets($fp);

		//Test if we receive the NodeHello
		//NodeHello error
		if (preg_match_all('/ProtocolError/si', $buffer, $match)) { 
			$error = "non ok connection\n";
			break; 
		}
		//NodeHello ok
		if (preg_match_all('/EndMessage/si', $buffer, $match)) { 
			$ok_hello = 1;
			break; 
		}
	}

	//If NodeHello ok we can continu
	if ($ok_hello == 1) {
		//Test the key to retrieve
		$out = "ClientGet\r\n";
		$out .= "URI=$addresse\r\n";
		$out .= "Identifier=Request Number One\r\n";
		$out .= "ReturnType=NONE\r\n";
		$out .= "Verbosity=1\r\n";
		$out .= "EndMessage\r\n";
		
		//Write command to the buffer
		fwrite($fp, $out);
		$count = 1;
		
		//Read the buffer
		while ( !feof($fp) )
		{  
			$buffer = fgets($fp);

			//Check if we can get the Key
			if (preg_match_all('/ExtraDescription/si', $buffer, $match)) { 
				$ExtraDescription = explode("=",$buffer);
				$ExtraDescription = $ExtraDescription[1];
				$error = "Get Failed : $ExtraDescription";
				break;
			}
			
			//Look at the ContentType
			if (preg_match_all('/Metadata.ContentType=/si', $buffer, $match)) { 
				$content_type = explode("=",$buffer);
				$content_type = $content_type[1];
				
				//Check if the ContentType is text or html
				if (preg_match_all('/text\/html/si', $content_type, $match)) { 	}
				
				//Stop if ContentType don't match
				else {
					$error = "Error Content Type";
					break;
				}
			}
	
			//On GetFailed increase count to 2
			if (preg_match_all('/GetFailed/si', $buffer, $match)) { 			
				$count = 2;
			}

			//Get the error description on error
			if (preg_match_all('/CodeDescription/si', $buffer, $match)) { 
				$CodeDescription = explode("=",$buffer);
				$CodeDescription = $CodeDescription[1];
				$error = "Get Failed : $CodeDescription";
			}
			
			//If no problem break after two EndMessage		
			if (preg_match_all('/EndMessage/si', $buffer, $match)) { 
				$count = $count + 1;
				if ($count > 2) { break; }
			}
		}
	
		//If no error have occured, the file
		if ($error == NULL) {
			$out = "ClientGet\r\n";
			$out .= "URI=$addresse\r\n";
			$out .= "Identifier=Request Number One\r\n";
			$out .= "ReturnType=DISK\r\n";
			$out .= "Filename=$filename\r\n";
			$out .= "Verbosity=0\r\n";
			$out .= "EndMessage\r\n";
			
			//Send command to buffer
			fwrite($fp, $out);
			
			//Read the buffer Output
			while ( !feof($fp) )
			{  
				$buffer = fgets($fp);

				//Another check for error
				if (preg_match_all('/CodeDescription/si', $buffer, $match)) { 
					$CodeDescription = explode("=",$buffer);
					$CodeDescription = $CodeDescription[1];
					$error = "Get Failed : $CodeDescription";
					break;
				}
				//If no problem exit after EndMessage
				if (preg_match_all('/EndMessage/si', $buffer, $match)) { 
					break; 
				}

				//No error on process
				$error = "all ok";		
			}
			
			//Closing connexion with FCP
			fclose($fp);
			
			//Open the local page
			$fp1 = fopen($filename,"r");
			
			//Get page filesize
			$filesize = filesize($filename);
			
			//Check if we can open the file
			if (!$fp1) {
				echo "error when try to open the file<br>\n";
			}
			
			//Send the content from $filename to $buffer
			else {
				while ( !feof($fp1) )
				{
					$buffer .= fgets($fp1,$filesize);
				}
			}
			
			//Close the file
			fclose($fp1);

			//Delete the local file
			unlink($filename);
		}
	}
	
echo $error;
}
?>