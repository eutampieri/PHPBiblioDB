<?php
include('tokenizr.php');
$key=exec('./jsonvalidator.out');
$data=base64_decode(file_get_contents('bibliodb-utenti.json'));
$utenti = json_decode(trim(mcrypt_decrypt('rijndael-128', $key, $data, 'ecb'),'{'),true);
if(isset($_POST['user'])&&isset($_POST['password'])){
    if(isset($utenti[0][$_POST['user']])){
	if($utenti[2][$_POST['user']]=="admin"){
	    if($utenti[0][$_POST['user']]==$_POST['password']){
		$token=setToken($_POST["user"],$_POST["password"],800);
		setcookie("token",$token, time()+860);
		header("Location: mgr.php");
	    }
	    else{
		header("Location: index.php?error=Password+errata&mode=login");
	    }
	}
	else{
	    header("Location: index.php?error=Utente+non+autorizzato&mode=login");
	}
    }
    else{
	header("Location: index.php?error=Utente+inesistente&mode=login");
    }
    die();
}
if(isset($_GET['js'])&&$_GET['js']=='true'){
    sleep(5);
    header("Location: mgr.php");
    die();
}
?>
