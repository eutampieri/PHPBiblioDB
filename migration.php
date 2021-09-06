<?php
include("res/config.php");

/*Passi:
0: Si vuole migrare?
1:Amministratore
2:Database principale
3:Chiave di decodifica
4: Utenti
5: Iscritti
*/
function makeDB(){
	$file_db = new PDO($dbUrl);
	$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$qry='CREATE  TABLE "Libri" ("ISBN" varchar2(17) primary key, "Titolo" TEXT, "Autore" TEXT)';
	$stmt = $file_db->prepare($qry);
	$stmt->execute();
	$stmt = $file_db->prepare("CREATE INDEX LibriTitolo on Libri (Titolo);");
	$stmt->execute();
	$stmt = $file_db->prepare("CREATE INDEX LibriAutore on Libri (Autore);");
	$stmt->execute();
	$qry='CREATE  TABLE "Copie" ("ID" varchar2(20) primary key, "ISBN" VARCHAR2(17), "Posizione" TEXT, "Disponibilita" BOOLEAN, "DataPrestito" DATETIME, "UtentePrestito" TEXT, FOREIGN KEY (ISBN) REFERENCES Libri(ISBN)';
	$stmt = $file_db->prepare($qry);
	$stmt->execute();
	$qry='CREATE  TABLE "Utenti" ("Utente" VARCHAR2(30) PRIMARY KEY, "Password" TEXT, "Master" BOOL)';
	$stmt = $file_db->prepare($qry);
	$stmt->execute();
	$qry='CREATE  TABLE "Sessioni" ("Token" VARCHAR(30) PRIMARY KEY, "IP" TEXT, "Scadenza" DATETIME, "Utente" TEXT)';
	$stmt = $file_db->prepare($qry);
	$stmt->execute();
	$qry='CREATE  TABLE Iscritti (ID VARCHAR(30) PRIMARY KEY, RFID TEXT, Nome TEXT, Cognome TEXT)';
	$stmt = $file_db->prepare($qry);
	$stmt->execute();
	$stmt = $file_db->prepare("CREATE INDEX IscrittiBadge ON Iscritti ( RFID(10) );");
	$stmt->execute();
	$file_db=NULL;
}
function SQLdate($date){
	$date=split("/",$date);
	$a=$date[2];
	$m=$date[1];
	$g=$date[0];
	return $a.'-'.$m.'-'.$g.' 00:00:00';
}
//Controllo database valido. Se si, die(), altriment migrazione
//
if(!is_file("bibliodb.sqlite")||is_file("conf")){
	if(is_file("conf")&&is_file("bibliodb.sqlite")){
		copy("bibliodb.sqlite","bibliodb.dbold");
	}
}
else{
	header("Location: index.php");
	die();
}
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
		else if($_GET["mig"]=="true"&&$_GET["stage"]=="fine"){
			echo file_get_contents("confDep/fine.html");
			unlink("conf");
		}
		else if($_GET["mig"]=="false"){
			echo file_get_contents("confDep/zero.html");
		}
		else if($_GET["mig"]=="true"){
			echo file_get_contents("confDep/migMainDb.html");
		}
		else if(isset($_POST["stage"])&&$_POST["stage"]=="admin"&&$_POST["mig"]=="false"){
			makeDB();
			$file_db = new PDO($dbUrl);
			$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$qry="INSERT INTO Utenti VALUES (:i,:d,1)";
			$stmt = $file_db->prepare($qry);
			$stmt->bindParam(':i',$_POST["utente"]);
			$stmt->bindParam(':d',password_hash($_POST["password"], PASSWORD_DEFAULT));
			$stmt->execute();
			echo file_get_contents("confDep/fine.html");
		}
		else if(isset($_POST["stage"])&&$_POST["stage"]=="migMainDb"&&$_POST["mig"]=="true"){
			makeDB();
			file_put_contents("conf","");
			$file_db = new PDO($dbUrl);
			$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			move_uploaded_file($_FILES["db"]["tmp_name"], "bibliodb.json");
			$db=json_decode(file_get_contents("bibliodb.json"),true);
			foreach($db[3] as $isbn=>$titolo){
				$qry="INSERT INTO Libri VALUES (:id, :isbn, :titolo, :aut, :pos, :disp, :dp, :own)";
				$titolo=ucwords($titolo," \t\r\n\f\v.");
				$aut=ucwords($db[4][$isbn]," \t\r\n\f\v.");
				$pos=$db[1][$isbn];
				$disp=$db[0][$isbn];
				$dp=$db[8][$isbn];
				if($dp!=null){
					$dp=SQLdate($dp);
				}
				$own=$db[6][$isbn];
				$stmt = $file_db->prepare($qry);
				$stmt->bindParam(':id',strval(uniqid("libro")));
				$stmt->bindParam(':isbn',$isbn);
				$stmt->bindParam(':titolo',$titolo);
				$stmt->bindParam(':own',$own);
				$stmt->bindParam(':aut',$aut);
				$stmt->bindParam(':pos',$pos);
				$stmt->bindParam(':disp',$disp);
				$stmt->bindParam(':dp',$dp);
				$stmt->execute();
				//echo $isbn.", ".$titolo.", ".$aut.", ".$pos.", ".$disp.", ".$dp.", ".$own."<br>\n";
			}
			unlink("bibliodb.json");
			echo file_get_contents("confDep/getKey.html");
		}
		else if(isset($_POST["stage"])&&$_POST["stage"]=="key"&&$_POST["mig"]=="true"){
			move_uploaded_file($_FILES["key"]["tmp_name"], "key.txt");
			echo file_get_contents("confDep/keyOK.html");
		}
		else if(isset($_POST["stage"])&&$_POST["stage"]=="dbs"&&$_POST["mig"]=="true"){
			move_uploaded_file($_FILES["users"]["tmp_name"], "bibliodb-utenti.json");
			move_uploaded_file($_FILES["iscritti"]["tmp_name"], "iscritti.json");
			$file_db = new PDO($dbUrl);
			$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$key=file_get_contents("key.txt");
			unlink("key.txt");
			$data=base64_decode(file_get_contents('bibliodb-utenti.json'));
			$utenti = json_decode(trim(mcrypt_decrypt('rijndael-128', $key, $data, 'ecb'),'{'),true);
			echo file_get_contents("confDep/dbs.html");
			foreach ($utenti[0] as $user => $pwd) {
				$master=false;
				if($utenti[2][$user]=="admin"){
					echo "<li>".$user."</li>\n";
					$master=true;
				}
				$pwhash=password_hash($pwd, PASSWORD_DEFAULT);
				$qry="INSERT INTO Utenti VALUES (:user, :pwhash, :master)";
				$stmt = $file_db->prepare($qry);
				$stmt->bindParam(':user',$user);
				$stmt->bindParam(':pwhash',$pwhash);
				$stmt->bindParam(':master',$master);
				$stmt->execute();
			}
			$jsonAddData=json_encode(["note"=>"Importato da BiblioDB","stato"=>"Azione aggiuntiva richiesta","code"=>500]);
			$iscritti=json_decode(file_get_contents("iscritti.json"))[1];
			foreach ($iscritti as $id => $nome) {
				$idIscritto=strval(uniqid("iscr"));
				$qry="INSERT INTO Iscritti VALUES (:id, :rfid, :nome, \"\", :json)";
				$stmt = $file_db->prepare($qry);
				$stmt->bindParam(':id',$idIscritto);
				$stmt->bindParam(':rfid',$id);
				$stmt->bindParam(':nome',$nome);
				$stmt->bindParam(':json',$jsonAddData);
				$stmt->execute();
			}
			unlink("iscritti.json");
			unlink("bibliodb-utenti.json");
			echo "</ul>";
			echo '<a href="migration.php?mig=true&stage=fine" data-role="button">Fine</a>';
		}
		?>
		</div>
	</div>
	</body>
</html>
