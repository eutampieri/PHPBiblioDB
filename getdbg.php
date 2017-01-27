<?php
if(isset($_GET['id'])&&is_file('/var/www/html/dbgcl/'.$_GET['id'].".csv")){
	header("Content-Type: text/csv");
	echo file_get_contents('/var/www/html/dbgcl/'.$_GET['id'].".csv");
	unlink('/var/www/html/dbgcl/'.$_GET['id'].".csv");
}
else{
	echo "<h1>Erorre: file non trovato</h1>";
}
?>