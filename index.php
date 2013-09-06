<?php
	class Connection{
		public $count = 1;
	}

	class Node{
		public $Connections = array();
		function addConnection($pageName){
			if(isset($this->Connections[$pageName])){
				$this->Connections[$pageName]->count ++;
			} else{
				$this->Connections[$pageName] = new Connection();
			}
		}
	}

	class User{
		public $location;
		function __construct($location){
			$this->location = &$location;
		}
	}

	$file = fopen('./apache.log.test', 'r');
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
			$users[$ip]->location->addConnection($pageName);
			$users[$ip]->location = &$page;
		} else{
			$users[$ip] = new User($page);
		}

	}

	//print_r($users);
	print_r($nodes);