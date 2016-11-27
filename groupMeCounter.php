<?php

//Jake Gluck

//Just fill in with your group and your access token
$accessToken = "";
$group = "";

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
$i = getUser();
$s = $i["response"];
$messages = $s["messages"];
$count = $s["count"];

echo "Proccesing. This may take a minute\n";
for ($c = 0;$c < $count;$c++){
	$end = ($messages[$c % 100]);
	$ID = $end["id"];
	$userID = $end["user_id"];
	$name = $end["name"];
	$text = $end["text"];
	if (array_key_exists($userID, $all_messages)){
		$temp = $all_messages[$userID];
		$temp[$ID] = $text;
		$all_messages[$userID] = $temp;
	}else{
		$temp = array();
		$temp[$ID] = $text;
		$all_messages[$userID] = $temp;
		$allUsers[$userID] = $name;
	}
	if ((($c + 1)% 100) == 0){
		$i = getUser2($ID);
		$s = $i["response"];;
		$messages = $s["messages"];
		$count = $s["count"];
		//echo "$c $ID\n";
	}
}

$numOfMember = count($all_messages);
echo "Total Number of Messages: $count\n";
echo "Number of Members: $numOfMember\n";
foreach ($all_messages as $key => $user){
    $numberOfUserMessages = count($user);
    $userName = $allUsers[$key];
	echo "$userName has sent $numberOfUserMessages messages\n";
}

?>