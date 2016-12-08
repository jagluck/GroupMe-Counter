<?php

//Jake Gluck

//Just fill in with your group and your access token, then run script
$accessToken = "";
$group = "";

//gets first 100 messages
function getUser() {
	global $accessToken,$group;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://api.groupme.com/v3/groups/$group/messages?limit=100&acceptFiles=true");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	$headers = array();
	$headers[] = 'GroupMe-Android/5630028';
	$headers[] = 'Content-Type: application/json';
	$headers[] = "X-Access-Token: $accessToken";

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	return $json = json_decode(curl_exec($ch), true);
}

//gets 100 messages
function getUser2($idw) {
	global $accessToken,$group,$id;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://api.groupme.com/v3/groups/$group/messages?limit=100&acceptFiles=true&before_id=$idw");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	$headers = array();
	$headers[] = 'GroupMe-Android/5630028';
	$headers[] = 'Content-Type: application/json';
	$headers[] = "X-Access-Token: $accessToken";

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	return $json = json_decode(curl_exec($ch), true);
}

$allUsers = array();
$all_messages = array();
$user_avg = array();
$likes = array();
$i = getUser();
$s = $i["response"];
$messages = $s["messages"];
$count = $s["count"];

echo "Proccesing. This may take a minute\n";

//reads every message in groupme group's history
for ($c = 0;$c < $count;$c++){
	$end = ($messages[$c % 100]);
	$ID = $end["id"];
	$userID = $end["user_id"];
	$name = $end["name"];
	$text = $end["text"];
	$favorited = $end["favorited_by"];
	$num_likes = sizeof($favorited);
	if (array_key_exists($userID, $all_messages)){
		$temp = $all_messages[$userID];
		$temp[$ID] = $text;
		$all_messages[$userID] = $temp;
		$temp2 = $likes[$userID];
		$likes[$userID] = ($temp2 + $num_likes);
	}else{
		$likes[$userID] = $num_likes;
		$temp = array();
		$temp[$ID] = $text;
		$all_messages[$userID] = $temp;
		$allUsers[$userID] = $name;
	}

	//loads a new set of 100 messages
	if ((($c + 1)% 100) == 0){
		$i = getUser2($ID);
		$s = $i["response"];;
		$messages = $s["messages"];
		$count = $s["count"];
	}
}

//calculates average number of likes
$numOfMember = count($all_messages);
echo "Total Number of Messages: $count\n";
echo "Number of Members: $numOfMember\n";
foreach ($likes as $key => $userTotalLikes){
	$user = $all_messages[$key];
	$numberOfUserMessages = count($user);
	if ($numberOfUserMessages != 0){
		$avgLikes = number_format((float)($userTotalLikes/$numberOfUserMessages), 2, '.', '');
    	$user_avg[$key] = $avgLikes;
	}else{
		$user_avg[$key] = 0.00;
	}
}

//Sorts average number of likes in decending order
arsort($user_avg);

//Prints each users stats
foreach ($user_avg as $key => $avgLikes){
	$user = $all_messages[$key];
    $numberOfUserMessages = count($user);
    $userName = $allUsers[$key];
    $userTotalLikes = $likes[$key];
	echo "$userName sent $numberOfUserMessages messages, $userTotalLikes total likes, and averages $avgLikes per message\n";
}
?>