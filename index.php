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
		preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}) .*"GET (\/.*\/) H/', $line, $info);
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
	}

	$maxWeight = 0;
	$maxPath = array();

	foreach($nodes as $pageName => $node){
		$found = findNodeMax($pageName, $node, $nodes);
		$sum = 0;
		foreach($found as $path){
			$sum += $path[1];
		}
		if($sum > $maxWeight){
			$maxWeight = $sum;
			$maxPath = $found;
		}
	}

	foreach($maxPath as $step){
		echo('"' . $step[0] . '", ');
	}
	echo(' appeared ' . $maxWeight . ' times.');
	

	function findNodeMax($pageName, $node, $nodes, $depth = 0){
		if($depth == 3){
			return array();
		} else{
			$max = 0;
			$maxPageName = '';
			foreach($node->links as $innerPageName => $count){
				if($count > $max){
					$max = $count;
					$maxPageName = $innerPageName;
				}
			}
			if(!$maxPageName) return array();
			$rtn = findNodeMax($maxPageName, $nodes[$maxPageName], $nodes, $depth + 1);
			$rtn[] = array($maxPageName, $max);
			return $rtn;
		}
	}