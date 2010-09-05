<?php
require 'config.php';

// Create our Application instance.
$facebook = new Facebook(array(
  'appId'  => FACEBOOK_APP_ID,
  'secret' => FACEBOOK_APP_SECRET,
  'cookie' => true
));

function convertEmoji($emoji,$text){
	$emoji_pattern = "/\:fe[0-9a-f]{3}\:/i";
    $result = $text;
    $matches = array();
    preg_match_all($emoji_pattern, $result, &$matches);
    
    $len = count($matches[0]);
    for($i=0; isset($matches[0])&& $i<$len;$i++){
      $match = trim($matches[0][$i]);
      if (!isset($emoji[$match])) continue;
      $code = ($emoji[$match]["code"]);
      $result = str_replace($match, $code, $result);           
    }
    return $result;
}
///////////////////
$session = $facebook->getSession();

if(isset($_POST['post_type'])){
	$message = stripslashes($_POST['status']);
	if($_POST['post_type'] === 'post'){
		////////////////////////////
		
		$url = $_POST['target_url'];
		$sharpIndex = strrpos($url,"#");
      	$text ="";
      		
      	if($sharpIndex > -1){
      		$text = substr($url,$sharpIndex+3);
      	} else {
      		$text = substr($url,strpos($url,"facebook.com")+13);
      	}
      	
      	$qid = strpos($text, "?");
      	$path = substr($text,0, $qid);
      	$q = substr($text,$qid+1);
      	// Build query dict
      	$t_qdic = explode('&',$q);
      	$qdic = array();
      	$t_len = count($t_qdic);
      	for($i=0;$i<$t_len;$i++){
      		$kv = explode('=', trim($t_qdic[$i]));
      		$qdic[trim($kv[0])] = urldecode(trim($kv[1]));
      	}
      	
      	// Extract user_id
      	if (!strpos($path,".php")){
      		$qdic['id'] = $path;
      		// We need to find what integer counterpart of id is 
      	}
      	if (isset($qdic['v']) && $qdic['v'] == "wall"){
      		//////////////// START GET USER ID //////////////////
      		$result = $facebook->api('/'.$qdic['id']);
      		$ep = '/'.$result['id'].'_'.$qdic['story_fbid'].'/comments';
      		$arguments = array(
			"message" => $message
			);
			$facebook->api($ep,"POST",$arguments);
			echo "Message has been posted.";
      		//////////////// END GET USER ID ////////////////////
      	}
      	else if ($path == "photo.php"){
      		$query ='SELECT pid, aid, object_id, link FROM photo WHERE aid in (select aid from album where owner='.$qdic["id"].')';
      		
      		$result = $facebook->api( array( 'method' => 'fql.query','access_token'=>$session['access_token'], 'query' => $query) );   
      		$r_len = count($result);
			$oid = "";
			for($i=0;$i<$r_len;$i++){
				if(strpos($result[$i]["link"],"pid=".$qdic['pid']."&")){
					//set oid
					$oid = $result[$i]['object_id'];
					break;	
				}	
			}
			
			$ep = "/".$oid."/comments";  
			$arguments = array(
				"message" => $message
			);
			$facebook->api($ep,"POST",$arguments); 		
			echo "Message has been posted.";
      	}

		////////////// END OF POST COMMENT //////////////
	} else{
		$endpoint = '/';
		//check if user select friend and has friend_selector_id
		if($_POST['post_type']=='friend'&&isset($_POST['friend_id'])){
			$endpoint .= $_POST['friend_id'];
		}	else {
			$endpoint .= 'me';
		}
			
		$endpoint .= '/feed';
		$arguments = array(
			"message" => $message
		);
		try{
			$facebook->api($endpoint,"POST",$arguments);		
			echo "Message has been posted.";

		} catch (FacebookApiException $e) {
		    echo "Error occurs.". $e;
  		}		
	}	
}
