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
			header("Location: ".gbooks($_GET["isbn"],"copertina",urldecode($_GET["titolo"]),urlencode($_GET["autore"])));
			break;
		case "ISBNRegistered":
			$qry='SELECT * FROM Libri WHERE ISBN = :q';
			$stmt = $file_db->prepare($qry);
			$stmt->bindParam(':q',$_GET["isbn"]);
			$stmt->execute();
			$libri=$stmt->fetchAll(PDO::FETCH_ASSOC);
			echo count($libri);
			break;
		default:
			break;
	}
}
?>