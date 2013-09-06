<?php

	class Node{
		public $links = array();
		function addConnection($pageName){
			if(isset($this->links[$pageName])){
				$this->links[$pageName] ++;
			} else{
				$this->links[$pageName] = 1;
			}
		}
	}

	$file = fopen('./apache.log', 'r');
	if(!$file) die('Could not find apache.log');

	$users = array();
	$nodes = array();

	while(($line = fgets($file)) !== false){
		$info = array();
		preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}) .*"GET (\/.*\/) HTTP/', $line, $info);
		$ip = $info[1];
		$pageName = $info[2];

		if(isset($nodes[$pageName])){
			$page = $nodes[$pageName];
		} else{
			$page = new Node();
			$nodes[$pageName] = $page;
		}
		
		if(isset($users[$ip])){
			$users[$ip]->addConnection($pageName);
			$users[$ip] = $page;
		} else{
			$users[$ip] = $page;
		}	

		echo($ip . ", " . $pageName . "\r\n");

	}

	//print_r($users);
	print_r($nodes);