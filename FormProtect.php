<?php
	session_start();

        session_register('keyCode');

	$gen	= $_REQUEST['gen'];

 	if(isset($gen))
	{
		include("codeconfig.php");

		if ($secEnabled == "true")
		{
			$referalURL	= $_SERVER['HTTP_REFERER'];
			
			$allowedSites	= explode("|", $secSiteList);

			$i = 0;

			list($trashValue, $returnValue)	= split('//', $referalURL, 2);
			list($referalURL, $trashValue)	= split('/', $returnValue, 2);

			unset($trashValue);
			unset($returnValue);

			while($allowedSites[$i] != NULL)
			{
				$convert = gethostbyname($allowedSites[$i]);

				if ($allowedSites[$i] == $referalURL || $convert == $referalURL)
				{
					$FormProtect = new FormProtect_Class;
					$FormProtect->createNewImage($FormProtect->createNewCode());
				}
				else
				{
					$FormProtect = new FormProtect_Class;
					$FormProtect->createRestrictedSite();
				}
				$i++;
			}
		}
		else
		{
			$FormProtect = new FormProtect_Class;
			$FormProtect->createNewImage($FormProtect->createNewCode());
		}
	}

	class FormProtect_Class
	{
		var $codeString = "";

		function createNewImage($codeValue)
		{
			include("codeconfig.php");

			$im			= imagecreate($x_size, $y_size);
			$textColor		= imagecolorallocate($im, $txtRed, $txtGreen, $txtBlue);
			$backgroundColor	= imagecolorallocate($im, $bgRed, $bgGreen, $bgBlue);
						
			imagefill($im, 0, 0, $backgroundColor);

			if ($renderGrid == "1")
			{
				$border = imagecolorallocate($im, $gridRed, $gridGreen, $gridBlue);
				
				for ($i = $squareSize; $i <= $x_size; $i += $squareSize)
				{
					imageline($im, $i, 0, $i, $y_size, $border);
				}

				for ($i= $squareSize; $i <= $y_size; $i += $squareSize) 
				{
					imageline($im, 0, $i, $x_size, $i, $border);
				}
			}

			$x = $txtHorizontal;

			mt_srand((double)microtime()*1000000);

			for ($i = 0; $i <= strlen($codeValue); $i++)
			{
				$rndPos		= mt_rand($txtVertRangeStart, $txtVertRangeStop);
				$rndFont	= mt_rand(4, 6);
		
				imagechar($im, $rndFont, $x, $rndPos, $codeValue[$i] , $textColor);

				$x = $x + $txtPixelSpace;
			}

			header("Content-type: image/png");
			imagepng($im);
			imagedestroy($im);
		}
		
		function createRestrictedSite()
		{
			$im			= imagecreate(330, 40);
			$textColor		= imagecolorallocate($im, 255, 255, 255);
			$backgroundColor	= imagecolorallocate($im, 0, 0, 0);
			
			imagefill($im, 0, 0, $backgroundColor);

			$string			= "You are not allowed to access this script.";
			imagestring($im, 3, 20, 13, $string, $textColor);

			header("Content-type: image/png");
			imagepng($im);
			imagedestroy($im);
		}

		function createNewCode()
		{
			include("codeconfig.php");

			$returnedChar	= "";
			$dupcheckReturn = "";

			for($i = 0; $i < $codeLength; $i++)
			{
				$returnedChar	= $this->getRandomCharacter();
				$dupcheckReturn = $this->checkForDuplicates($codeString, $returnedChar);
	
				while($dupcheckReturn == "true")
				{
					$returnedChar	= $this->getRandomCharacter();
					$dupcheckReturn = $this->checkForDuplicates($codeString, $returnedChar);
				}
				
				$codeString .= $returnedChar;
			}

			$_SESSION['keyCode'] = md5($codeString);

			return ($codeString);
		}

		function getRandomCharacter()
		{
			mt_srand((double)microtime()*1000000);
			
			switch(mt_rand(1, 3))
			{
				case 1:
					$tempChar = chr(mt_rand(48, 57));
					break;
				case 2:
					$tempChar = chr(mt_rand(65, 90));
					break;
				case 3:
					$tempChar = chr(mt_rand(97, 122));
					break;
			}
	
			return ($tempChar);
		}

		function checkForDuplicates($inputString, $tempChar)
		{
			if(preg_match("/" . $tempChar . "/i", $inputString))
			{
				return "true";
			}
			else
			{
				return "false";
			}		
		}

		function verifyCode($codeValue)
		{
			if (md5($codeValue) == $_SESSION['keyCode'])
			{
				return "true";
			}
			else
			{
				return "false";
			}
		}
	}
?>
				