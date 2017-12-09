<?php

require_once('config.php'); 

if(file_exists($controlFile)){
    
	$xml = simplexml_load_file($controlFile);
	foreach($xml->children() as $child) {
        $role = $child->attributes();
		$state   = "";
		$codeOn  = "";
		$codeOff = "";
		$timeOn  = "";
		$timeOff = "";	
		foreach($child as $key => $value) {
				if ($key=="state") 
					$state = $value;
				if ($key=="code-on") 
					$codeOn = $value;
				if ($key=="code-off") 
					$codeOff = $value;
				if ($key=="time-on") 
					$timeOn = $value;
				if ($key=="time-off") 
					$timeOff = $value;	
		}
		
		if ( (strlen($timeOn) > 3) and (strlen($timeOff) > 3))
		{
			$date = date_create();
			$aktDate  = $date->format('Y-m-d H:i:s');
			$onDate   = $date->format('Y-m-d').' '.$timeOn.':00';
			$offDate  = $date->format('Y-m-d').' '.$timeOff.':00';
			if ( strtotime($onDate) >  strtotime($offDate) )
			{ 	
				$date = new DateTime(date("Y-m-d"));
				$date->modify('+1 day');
				$offDate  = $date->format('Y-m-d').' '.$timeOff.':00';
			}
			if ( (strtotime($aktDate) > strtotime($onDate)) and ( strtotime($offDate) > strtotime($aktDate)  ) )
				echo $output = shell_exec('sudo /home/pi/433Utils/RPi_utils/codesend '.$codeOn);
			else
				echo $output = shell_exec('sudo /home/pi/433Utils/RPi_utils/codesend '.$codeOff);	
		}
		elseif ( $state == 'on' )
			echo $output = shell_exec('sudo /home/pi/433Utils/RPi_utils/codesend '.$codeOn);
		else
			echo $output = shell_exec('sudo /home/pi/433Utils/RPi_utils/codesend '.$codeOff);
	}
}	
?>	
	
