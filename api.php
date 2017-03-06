<?php
include("res/bibliodb.php");
$file_db = new PDO('sqlite:bibliodb.sqlite');
$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if(isset($_GET["mode"])){
	switch ($_GET["mode"]){
		case "time":
			echo "#BEGIN MESSAGE#\n";
			echo "EpochTime: ";
			$dtz = new DateTimeZone('Europe/Rome');
			$time_in_sofia = new DateTime('now', $dtz);
			$offset=$dtz->getOffset( $time_in_sofia );
			echo time()+$offset;
			echo "\n";
			echo "Offset: ";
			echo $offset;
			echo "\n";
			echo "#END MESSAGE#\n";
			break;
		case "copertina":
			header("Location: ".str_replace("http://","https://",gbooks($_GET["isbn"],"copertina",urldecode($_GET["titolo"]),urlencode($_GET["autore"]))));
			break;
		case "titolo":
			echo gbooks($_GET["isbn"],"titolo",urldecode($_GET["titolo"]),urlencode($_GET["autore"]));
			break;
		case "autore":
			echo gbooks($_GET["isbn"],"autore",urldecode($_GET["titolo"]),urlencode($_GET["autore"]));
			break;
		case "ISBNRegistered":
			$qry='SELECT * FROM Libri WHERE ISBN = :q';
			$stmt = $file_db->prepare($qry);
			$stmt->bindParam(':q',$_GET["isbn"]);
			$stmt->execute();
			$libri=$stmt->fetchAll(PDO::FETCH_ASSOC);
			echo count($libri);
			break;
		case 'rcn':
			if(!is_file("rcn.json")){
				file_put_contents("rcn.json","{}");
			}
			$rcn=json_decode(file_get_contents("rcn.json"),true);
			header("Content-type:text/plain");
			if(isset($_GET['ean'])){
				$esiste=true;
				if(isset($rcn[$_GET['ean']])){
					echo "Registrato";
				}
				else{
					if($_GET["register"]=="true"){
						echo "Aggiunto";
						$rcn[$_GET['ean']]=true;
						file_put_contents("rcn.json", json_encode($rcn));
					}
					else{
						echo "Disponibile";
					}
				}
			}
			else{
				$max=0;
				foreach($rcn as $r=>$lsdjnhjlsdnh){
					if(strlen($r)==13 && $r[0]=="2"&&intval(ltrim(substr($r,1,-1),"0"))>$max){
						$max=intval(ltrim(substr($r,1,-1),"0"));
					}
				}
				$max=strval($max+1);
				$rcn="2";
				for($i=0;$i<11-strlen($max);$i++){
					$rcn=$rcn."0";
				}
				$rcn=checkDigitEAN13($rcn.$max);
				echo $rcn;
			}
		default:
			break;
	}
}
?>