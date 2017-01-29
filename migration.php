<?php
/*Passi:
0: Si vuole migrare?
1:Amministratore
2:Database principale
3:Chiave di decodifica
4: Utenti
5: Iscritti
*/
function makeDB(){
	$file_db = new PDO('sqlite:bibliodb.sqlite');
	$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$qry='CREATE  TABLE "Libri" ("ISBN" TEXT, "Titolo" TEXT, "Autore" TEXT, "Posizione" TEXT, "Disponibilita" INTEGER, "DataPrestito" DATETIME, "Proprietario" TEXT)';
	$stmt = $file_db->prepare($qry);
	$stmt->execute();
	$qry='CREATE  TABLE "Utenti" ("Utente" TEXT, "Password" TEXT, "Master" BOOL)';
	$stmt = $file_db->prepare($qry);
	$stmt->execute();
	$qry='CREATE  TABLE "Sessioni" ("Token" TEXT, "IP" TEXT, "Scadenza" DATETIME, "Utente" TEXT)';
	$stmt = $file_db->prepare($qry);
	$stmt->execute();
	$qry='CREATE  TABLE Iscritti (ID TEXT, RFID TEXT, Nome TEXT, Cognome TEXT, Dati TEXT)';
	$stmt = $file_db->prepare($qry);
	$stmt->execute();
	$file_db=NULL;
}
//Controllo database valido. Se si, die(), altriment migrazione
//
/*include('tokenizr.php');
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
$loggedIn=false;
if(isset($_COOKIE['token'])){
	$u=getToken($_COOKIE['token'])['user'];
	$p=getToken($_COOKIE['token'])['pwd'];
	if(isset($utenti[0][$u])){
	if($utenti[2][$u]=="admin"){
		if($utenti[0][$u]==$p){
		$loggedIn=true;
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
}
else{
	header("Location: index.php#login");
}*/
?>
<html>
	<head>
	<meta charset="utf-8">
	<title>
		Migrazione a BiblioDB PHP
	</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="favicon.ico">
	<link rel="stylesheet" href="css/jquery.mobile-1.4.5.min.css" />
	<script src="js/jquery-1.11.1.min.js"></script>
	<script type="text/javascript">
	 $(document).bind("mobileinit", function () {
		 $.mobile.ajaxEnabled = false;
	 });
	</script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
	<script src="js/sha512.js"></script>
	<script src="js/bibliodb.js"></script>
	<script>
		function check(){
			var a=document.getElementById("pwd1").value;
			var b=document.getElementById("pwd2").value;
			if(a!=b){
				document.getElementById("mismatch").className="mismatch";
				document.getElementById("pwdbtn").disabled=true;
			}
			else{
				document.getElementById("mismatch").className="nascosto";
				document.getElementById("pwdbtn").disabled=false;
			}
		}
	</script>
	<style>
	.mismatch{
		color:#E80E0C;
	}
	td{
		max-width: 33%;
	}
	img{
		max-height: 10em;
		/*max-width: 33%*/
		min-width: 2em;
	 }
	 .nascosto{
		 display:none;
	 }
	 .progressBar{
		 height:2em;
		 background-color:#ddd;
		 width:100%;
		 border-radius:0.5em;
	 }
	 .bar{
		 background-color:#6084E8;
		 border-radius:0.5em;
		 height:100%;
		 display:inline-block;
	 }
	 .percentage{
		 height:100%;
		 vertical-align:middle;
		 position: relative;
		 top: -33%;
	 }
	</style>
	</head>
	<body>
	<div data-role="page" data-control-title="Home" id="page1">
		<div data-theme="a" data-role="header" data-position="fixed">
		<h3>
			Configurazione
		</h3>
		</div>
		<div data-role="content">
		<?php
		if(!isset($_GET["mig"])&&!isset($_POST["mig"])){
			echo file_get_contents("confDep/mig.html");
		}
		else if($_GET["mig"]=="false"){
			echo file_get_contents("confDep/zero.html");
		}
		else if($_GET["mig"]=="true"){
			echo file_get_contents("confDep/migMainDb.html");
		}
		else if(isset($_POST["stage"])&&$_POST["stage"]=="admin"&&$_POST["mig"]=="false"){
			makeDB();
			$file_db = new PDO('sqlite:bibliodb.sqlite');
			$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$qry="INSERT INTO Utenti VALUES (:i,:d,1)";
			$stmt = $file_db->prepare($qry);
			$stmt->bindParam(':i',$_POST["utente"]);
			$stmt->bindParam(':d',hash("sha256",$_POST["password"]));
			$stmt->execute();
			echo file_get_contents("confDep/fine.html");
		}
		else if(isset($_POST["stage"])&&$_POST["stage"]=="migMainDb"&&$_POST["mig"]=="true"){
			makeDB();
			$file_db = new PDO('sqlite:bibliodb.sqlite');
			$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			var_dump($_FILES);
			move_uploaded_file($_FILES["db"]["tmp_name"], "bibliodb.json");
			$db=json_decode(file_get_contents("bibliodb.json"),true);
			foreach($db[3] as $isbn=>$titolo){
				$qry="INSERT INTO Libri VALUES (:isbn, :titolo, :aut, :pos, :disp, :dp, :own)";
				$titolo=ucwords($titolo," \t\r\n\f\v.");
				$aut=ucwords($db[4][$isbn]," \t\r\n\f\v.");
				$pos=$db[1][$isbn];
				$disp=$db[0][$isbn];
				$dp=$db[8][$isbn];
				if($dp==NULL){
					$dp="1970-01-01 00:00:00";
				}
				$own=$db[6][$isbn];
				$stmt = $file_db->prepare($qry);
				$stmt->bindParam(':isbn',$isbn);
				$stmt->bindParam(':titolo',$titolo);
				$stmt->bindParam(':own',$own);
				$stmt->bindParam(':aut',$aut);
				$stmt->bindParam(':pos',$pos);
				$stmt->bindParam(':disp',$disp);
				$stmt->bindParam(':dp',$dp);
				$stmt->execute();
				echo $isbn.", ".$titolo.", ".$aut.", ".$pos.", ".$disp.", ".$dp.", ".$own."<br>\n";
			}
			unlink("bibliodb.json");
		}
		?>
		</div>
		<div data-role="footer" data-position="fixed">  
		<div data-role="navbar" data-iconpos="top" data-theme="a">
			<ul>
			<li>
				<a  href="index.php?mode=pos" data-transition="fade" data-theme="" data-icon="info">
				Lista Pos.
				</a>
			</li>

			</ul>
		</div>
		</div>
	</div>
	</body>
</html>
