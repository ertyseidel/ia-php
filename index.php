<?php
	$file = fopen('./apache.log', 'r');
	if(!$file) die('Could not find apache.log');
	$info = array();
	while(($line = fgets($file)) !== false){
		preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}) .*"GET (\/.*\/) HTTP/', $line, $info);
		$ip = $info[1];
		$page = $info[2];
		echo($ip . ' visited '. $page . '<br />');

	}
