<?php

	/*
	 * Defining a utility class here to keep track of each user as they move around the site
	 * Since it really only contains one element, I could probably just have each user be an
	 * element in an associative array, but this works for now and keeps things nicely
	 * encapsulated.
	 */
	class User{
		public $path = array(0=>"", 1=>"", 2=>""); //keep track of the last three sites the user visited
		function addVisit($pageName, &$visits){
			//first we push the next site onto the path
			$this->path[0] = $this->path[1];
			$this->path[1] = $this->path[2];
			$this->path[2] = $pageName;
			//construct the path string (which is just the three paths)
			//in a real production env, I would probably make this more complex to avoid max string lengths
			//but this works for now
			$pathString = '"' . $this->path[0] . '", "' . $this->path[1] . '", ' . $this->path[2] . '"';
			//add the path string to the visits object (which is passed by reference)
			if(isset($visits[$pathString])){
				$visits[$pathString] ++;
			} else{
				$visits[$pathString] = 1;
			}
		}
	}

	$file = fopen('./apache.log', 'r'); //okay, let's start!
	if(!$file) die('Could not find apache.log');

	$users = array();
	$visits = array();

	while(($line = fgets($file)) !== false){
		$info = array();

		//get the IP and page name, and put them in the (temp) $info array
		//An optimization here would be to put info outside of the while loop
		//but that requires further testing and is only worth a few ms anyway
		preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}) .*"GET (\/.*\/) H/', $line, $info);
		$ip = $info[1];
		$pageName = $info[2];
		
		//create the user or update an existing one, based on IP
		if(isset($users[$ip])){
			$users[$ip]->addVisit($pageName, $visits);
		} else{
			$users[$ip] = new User();
			$users[$ip]->addVisit($pageName, $visits);
		}
	}

	//find the maximum path
	$maxCount = 0;
	$maxPath = "";
	foreach($visits as $path => $visitCount){
		if($visitCount > $maxCount){
			$maxCount = $visitCount;
			$maxPath = $path;
		}
	}

	//and print it. Since we pretty-printed the paths when we put them in
	//the user object, we can just smash it into the echo string here.
	print("The path " . $maxPath . " was visited " . $maxCount . " times.");

