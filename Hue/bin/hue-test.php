<?php

	define('EXCEPTION_LOG_FILE', 'error_log');
	require_once('/var/www/ember/system/include/common.inc.php');
	#Debug::enable();	
	
	$ip = Site::getSetting('hue_ip');
	$hash = Site::getSetting('hue_token');
	
	
	$base = new Hue_Base($ip, $hash);
	
	$light = $base->getLightObj(1);
	
	echo 'Dim'.PHP_EOL;	
	$light->setBrightness(1);
	$light->setTransitionTime(10);
	$base->sendLight($light);
	
	sleep(10);
	echo 'Off'.PHP_EOL;	
	$light->setON(false);
	$light->setBrightness(1);
	$light->setTransitionTime(0);
	$base->sendLight($light);

	sleep(5);
	echo 'On'.PHP_EOL;	
	$light->colorMode(Hue_Light::MODE_CT);
	$light->setON(true);
	$light->clearTransitionTime();
	$light->setBrightness(254);
	$base->sendLight($light);
	
	echo PHP_EOL;
