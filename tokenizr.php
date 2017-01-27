<?php
function setToken($user,$pass,$exp){
	$uuid=uniqid();
	$filename="pub/".$uuid.".tk";
	$arr=array('user'=>$user,'pwd'=>$pass,"date"=>date('U'),"exp"=>$exp);
	file_put_contents($filename, json_encode($arr));
	return $uuid;
}
function getToken($token){
	$tkString=file_get_contents("pub/".$token.".tk");
	$arr=json_decode($tkString,true);
	if((intval($arr["date"])+intval($arr["exp"]))>intval(date('U'))){
		return array("user"=>$arr["user"],"pwd"=>$arr["pwd"]);
	}
	else{
		unlink("pub/".$token.".tk");
		return false;
	}
}
function killToken($token){
	unlink("pub/".$token.".tk");
}
