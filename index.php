<?php

	class User{
		public $path = array(0=>"", 1=>"", 2=>"");
		function addVisit($pageName, &$visits){
			$this->path[0] = $this->path[1];
			$this->path[1] = $this->path[2];
			$this->path[2] = $pageName;
			$pathString = '"' . $this->path[0] . '", "' . $this->path[1] . '", ' . $this->path[2] . '"';
			if(isset($visits[$pathString])){
				$visits[$pathString] ++;
			} else{
				$visits[$pathString] = 1;
			}
		}
	}

	$file = fopen('./apache.log', 'r');
	if(!$file) die('Could not find apache.log');

	$users = array();
	$visits = array();

	while(($line = fgets($file)) !== false){
		$info = array();
		preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}) .*"GET (\/.*\/) H/', $line, $info);
		$ip = $info[1];
		$pageName = $info[2];
		
		if(isset($users[$ip])){
			$users[$ip]->addVisit($pageName, $visits);
		} else{
			$users[$ip] = new User();
			$users[$ip]->addVisit($pageName, $visits);
		}
	}

	$maxCount = 0;
	$maxPath = "";
	foreach($visits as $path => $visitCount){
		if($visitCount > $maxCount){
			$maxCount = $visitCount;
			$maxPath = $path;
		}
	}

	print("The path " . $maxPath . " was visited " . $maxCount . " times.");
