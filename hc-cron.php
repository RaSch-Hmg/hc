<?php


/*
sudo visudo
www-data ALL=NOPASSWD:/home/pi/433Utils/RPi_utils/codesend
www-data ALL=NOPASSWD:/home/pi/raspberry-remote/send


*/

$shellEx ="sudo /home/pi/raspberry-remote/send ";

require_once('config.php'); 

if(file_exists($controlFile)){

	$xml = simplexml_load_file($controlFile);
	foreach($xml->children() as $child) {
        $role = $child->attributes();
		$state   = "";
		$code  = "";
		$timeOn  = "";
		$timeOff = "";	
		foreach($child as $key => $value) {
				if ($key=="state") 
					$state = $value;
				if ($key=="code") 
					$code = $value;
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
				$output = shell_exec($shellEx.$code." 1");
			else
				$output = shell_exec($shellEx.$code." 0");	
		}
		elseif ( $state == 'on' )
			$output = shell_exec($shellEx.$code." 1");
		else
			$output = shell_exec($shellEx.$code." 0");
	}	
}	
?>	
	
