<?php

define( 'APPLICATION_LOADED', true );
header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');


/*
sudo visudo

www-data ALL=NOPASSWD:/home/pi/433Utils/RPi_utils/codesend

chmod 777

*/


//$output = shell_exec('sudo /home/pi/433Utils/RPi_utils/codesend 5510420');
//echo "$output";
require_once('config.php'); 

if(!file_exists($controlFile)){

	$dom=simplexml_load_string("<list></list>");
	$dom->addAttribute('type', 'config');

	$count = count($ConfigParams);
	for ($i = 0; $i < $count; $i++) {
		addConfigParams($dom , $i , $ConfigParams[$i]['name'] ,0 , 0, 0 , $ConfigParams[$i]['codeOn'] , $ConfigParams[$i]['codeOff']);
	}
	$xml=$dom->asXML();

	$fp = fopen($controlFile, 'w');
	fwrite($fp, $xml);
    fclose($fp);
}



function addConfigParams($dom ,$type , $name ,$state , $timeOn, $timeOff , $codeOn , $codeOff){
	$item = $dom->addChild("item");
	$item->addAttribute('type', $type);
	$item->addChild('name', $name);
	$item->addChild('state', $state);
	$item->addChild('time-on', $timeOn);
	$item->addChild('time-off', $timeOff);
	$item->addChild('code-on', $codeOn);
	$item->addChild('code-off', $codeOff);
}

function printEditParams($type , $name , $state , $timeOn, $timeOff ){
?>
	<div class="callout small">
	<div class="row">
		<div class="small-12 columns">
			<p><?Php echo $name;?></p>
			<div class="switch large">
			  <input class="switch-input" id="state-<?Php echo $type;?>" type="checkbox" name="state-<?Php echo $type;?>" >
			  <label class="switch-paddle" for="state-<?Php echo $type;?>">
				<span class="show-for-sr"><?Php echo $name;?></span>
				<span class="switch-active" aria-hidden="true">On</span>
				<span class="switch-inactive" aria-hidden="true">Off</span>
			  </label>
			</div>
		</div>
	</div>	

	<div class="row">
		<div class="small-6 columns">
			<label>
				On von:
				<input type="time" name="timeOn-<?Php echo $type;?>" value="<?php echo $timeOn; ?>" >
			</label>
		</div>
		<div class="small-6 columns">
			<label>
				On bis:
				<input type="time" name="timeOff-<?Php echo $type;?>" value="<?php echo $timeOff; ?>">
			</label>
		</div>

	</div>	
	<script>
		if ( '<?php echo $state; ?>' == 'on' ) 
			$('#state-<?Php echo $type;?>').prop('checked', true); 
	</script>
	</div>
		
<?php
}


if( isset($_POST['submit']) ) {

	$dom=simplexml_load_string("<list></list>");
	$dom->addAttribute('type', 'config');
	
	$count = count($ConfigParams);
	for ($i = 0; $i < $count; $i++) {
		addConfigParams($dom , $i , $ConfigParams[$i]['name'] ,@$_POST['state-'.$i] , $_POST['timeOn-'.$i], $_POST['timeOff-'.$i] , $ConfigParams[$i]['codeOn'] , $ConfigParams[$i]['codeOff']);
	}
	$xml=$dom->asXML();

	$fp = fopen($controlFile, 'w');
	fwrite($fp, $xml);
    fclose($fp);
}

?> 

<!doctype html>
<html class="no-js" lang="de">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home Control 1.0</title>
	<link rel="stylesheet" href="fd6.3/css/foundation.min.css" />
    <link rel="stylesheet" href="fd6.3/css/app.css" />
	<link rel="stylesheet" href="font-awesome/css/font-awesome.min.css" />
	<script src="fd6.3/js/vendor/jquery.js"></script>
	
	<style>
		html, body {
			height: 100%;
		}
	
		.row-block{
			border:1px solid #D1D1D1;
			display: block;
		}
		
		.top-bar{
			margin-bottom:2rem;
			background-color:#2C3840;
			color:#fff;
			padding-left:1rem;
		}
		
		footer {
		  position:absolute;
		  padding: 10px;
		  bottom: 0;
		  left: 0;
		  right: 0;
		  color:#fff;
		  background-color:#2C3840;
		}
	
	</style>
	
	
  </head>
<body>

<div class="top-bar">
  <div class="top-bar-left">
	<h2> Home-Control 1.0 </h2>
  </div>
  <div class="top-bar-right">
  </div>
</div>


<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">

<?php

	$xml = simplexml_load_file($controlFile);
	$Akey = Array();
	foreach($xml->children() as $child) {
        $role = $child->attributes();
		$name 	 = "";
		$state 	 = "";
		$timeOn  = "";
		$timeOff = "";	
		foreach($child as $key => $value) {
				$Akey[$key.$role]=$value;
				if ($key=="name") 
					$name = $value;
				if ($key=="state") 
					$state = $value;
				if ($key=="time-on") 
					$timeOn = $value;
				if ($key=="time-off") 
					$timeOff = $value;	
		}
		printEditParams($role , $name , $state , $timeOn, $timeOff );
	}
?>	
	
	<div class="row" style="margin-top:2rem;" >
		<div class="small-12 medium-4 columns">
			<input type="submit" name="submit" class="success button expanded" value="Speichern"  />
		</div>
	</div>	
	
</form>	
<footer>
  <div class="large-12">
    <div class="panel">
      <p class="nowrap text-center"></p>
    </div>
  </div>
</footer>
<script src="fd6.3/js/vendor/what-input.js"></script>
<script src="fd6.3/js/vendor/foundation.min.js"></script>
<script src="fd6.3/js/app.js"></script>

</body>
</html>


