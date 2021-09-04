<?php
include("res/bibliodb.php");
if(!is_file("bibliodb.sqlite")){
	header("Location: migration.php");
	die();
}
?>
<html>
<head>
	<meta charset="utf-8">
	<title>
		BiblioDB
	</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<!-- For non-Retina (@1× display) iPhone, iPod Touch, and Android 2.1+ devices: -->
	<link rel="res/icons/apple-touch-icon" href="res/icons/apple-touch-icon.png"><!-- 57×57px -->
	<!-- For the iPad mini and the first- and second-generation iPad (@1× display) on iOS = 6: -->
	<link rel="res/icons/apple-touch-icon" sizes="72x72" href="res/icons/apple-touch-icon-72x72.png">
	<!-- For the iPad mini and the first- and second-generation iPad (@1× display) on iOS = 7: -->
	<link rel="res/icons/apple-touch-icon" sizes="76x76" href="res/icons/apple-touch-icon-76x76.png">
	<!-- For iPhone with @2× display running iOS = 6: -->
	<link rel="res/icons/apple-touch-icon" sizes="114x114" href="res/icons/apple-touch-icon-114x114.png">
	<!-- For iPhone with @2× display running iOS = 7: -->
	<link rel="res/icons/apple-touch-icon" sizes="120x120" href="res/icons/apple-touch-icon-120x120.png">
	<!-- For iPad with @2× display running iOS = 6: -->
	<link rel="res/icons/apple-touch-icon" sizes="144x144" href="res/icons/apple-touch-icon-144x144.png">
	<!-- For iPad with @2× display running iOS = 7: -->
	<link rel="res/icons/apple-touch-icon" sizes="152x152" href="res/icons/apple-touch-icon-152x152.png">
	<!-- For iPhone 6 Plus with @3× display: -->
	<link rel="res/icons/apple-touch-icon" sizes="180x180" href="res/icons/apple-touch-icon-180x180.png">
	<link rel="icon" href="favicon.ico">
	<link rel="stylesheet" href="css/jquery.mobile-1.4.5.min.css" />
	<script src="js/jquery-1.11.1.min.js"></script>
	<!--script type="text/javascript">
	$(document).bind("mobileinit", function () {
	    $.mobile.ajaxEnabled = false;
	});
	</script-->
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
	<script src="js/sha512.js"></script>
	<script src="js/bibliodb.js"></script>
	<style type="text/css">
	img{
		max-height: 10em;
	}
	.nascosto{
		display:none;
	}
	</style>
</head>
<body onload="if(getOS()=='iOS'||getOS()=='Android'){document.getElementById('isbnscan').className='ui-link ui-btn ui-shadow ui-corner-all';}">
	<div data-role="page" data-control-title="Home" id="page1">
		<div data-theme="a" data-role="header" data-position="fixed">
			<h3>
				BiblioDB
			</h3>
		</div>
		<div data-role="content">
			<?php
			if(!isset($_GET['mode'])){
				echo'
				<h1>Lista Libri</h1>';
			}
			else{
				switch ($_GET['mode']) {
					case 'titolo':
					echo "<h1>Ricerca per titolo</h1>";
					break;
					case 'autore':
					echo "<h1>Ricerca per autore</h1>";
					break;
					case 'isbn':
					echo "<h1>Ricerca per ISBN</h1>";
					break;
					case 'posizione':
					echo "<h1>Elenco libri in ".$_GET['q']."</h1>";
					break;
					case 'pos':
					echo "<h1>Elenco libri per posizione</h1>";
					break;
					case 'login':
					echo "<!--";
					break;
					case 'all':
					echo'<h1>Lista Libri</h1>';
					break;
					default:
					echo'<h1>Lista Libri</h1>';
					unset($_GET['mode']);
					break;
				}
			}
			?>
			<form method="get">
				<div data-role="fieldcontain" data-controltype="textinput" id="titolo">
					<input name="q" id="textinput2" placeholder="" value="" type="text">
				</div>
				<div data-role="fieldcontain" data-controltype="selectmenu" style="text-align:center;">
					<select id="titoaut" name="mode">
						<option value="titolo"<?php if($_GET['mode']=="titolo"){echo" selected";}?>>
							Titolo
						</option>
						<option value="autore"<?php if($_GET['mode']=="autore"){echo" selected";}?>>
							Autore
						</option>
						<option value="isbn"<?php if($_GET['mode']=="isbn"){echo" selected";}?>>
							ISBN
						</option>
						<option value="posizione"<?php if($_GET['mode']=="posizione"){echo" selected";}?>>
							Posizione
						</option>
					</select>
				</div>
				<input type="submit" value="Cerca">
			</form>
			<a class="nascosto" id="isbnscan" data-role="button" href="pic2shop://scan?callback=http%3A//serverseutampieri.ddns.net/nas/Eugenio/catlibri/%3Fmode%3Disbn">
				Scansiona un ISBN
			</a>
					<?php
					$file_db = new PDO('sqlite:bibliodb.sqlite');
					$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					if(!isset($_GET['mode'])){
						$qry='SELECT * FROM Libri LIMIT 10';
						$stmt = $file_db->prepare($qry);
						$stmt->execute();
						$libri=$stmt->fetchAll(PDO::FETCH_ASSOC);
						echo "			<table>
				<tr>
";
						foreach ($libri as $libro) {
							echo "<td><img src=\"";
							echo str_replace("http://","https://",gbooks($libro["ISBN"],"copertina",urlencode($libro["Titolo"]),urlencode($libro["Autore"])));
							echo "\"></td><td>";
							echo "ISBN: ";
							echo $libro["ISBN"];
							echo "<br>Titolo: ";
							echo  $libro["Titolo"];
							echo "</br>Autore: ";
							echo  $libro["Autore"];
							echo "</br>Posizione: ";
							echo $libro["Posizione"];
							echo "<br>Stato: ".statoLibro($libro["Disponibilita"],$libro["DataPrestito"]);
				#echo "<br>Tempo: ".strval((intval(microtime(true)/1000)/1000)-$time)."s, +".strval(microtime(true)-$ptime);
							echo "</td></tr>\n";

						}
						echo "</table>";
						echo '<a href="?mode=all">Mostra la lista completa (Pu&ograve; richiedere molto tempo...)</a>'."\n";
					}
					else{
						switch ($_GET['mode']) {
							case 'all':
								$qry='SELECT * FROM Libri';
								$stmt = $file_db->prepare($qry);
								$stmt->execute();
								$libri=$stmt->fetchAll(PDO::FETCH_ASSOC);
								echo "			<table>
						<tr>
		";
								foreach ($libri as $libro) {
									echo "<td><img src=\"";
									echo str_replace("http://","https://",gbooks($libro["ISBN"],"copertina",urlencode($libro["Titolo"]),urlencode($libro["Autore"])));
									echo "\"></td><td>";
									echo "ISBN: ";
									echo $libro["ISBN"];
									echo "<br>Titolo: ";
									echo  $libro["Titolo"];
									echo "</br>Autore: ";
									echo  $libro["Autore"];
									echo "</br>Posizione: ";
									echo $libro["Posizione"];
									echo "<br>Stato: ".statoLibro($libro["Disponibilita"],$libro["DataPrestito"]);
									echo "</td></tr>\n";
								}
								echo "</table>";
									break;
							case 'titolo':
							$qry='SELECT * FROM Libri WHERE Titolo LIKE :q';
							$stmt = $file_db->prepare($qry);
							$ricerca="%".$_GET['q']."%";
							$stmt->bindParam(':q',$ricerca);
							$stmt->execute();
							$libri=$stmt->fetchAll(PDO::FETCH_ASSOC);
							echo "<table>";
							foreach($libri as $libro){
								echo "<td><img src=\"";
									echo str_replace("http://","https://",gbooks($libro["ISBN"],"copertina",urlencode($libro["Titolo"]),urlencode($libro["Autore"])));
									echo "\"></td><td>";
									echo "ISBN: ";
									echo $libro["ISBN"];
									echo "<br>Titolo: ";
									echo  $libro["Titolo"];
									echo "</br>Autore: ";
									echo  $libro["Autore"];
									echo "</br>Posizione: ";
									echo $libro["Posizione"];
									echo "<br>Stato: ".statoLibro($libro["Disponibilita"],$libro["DataPrestito"]);
									echo "</td></tr>\n";
							}
							echo "</table>";
							break;
							case 'autore':
							$qry='SELECT * FROM Libri WHERE Autore LIKE :q';
							$stmt = $file_db->prepare($qry);
							$ricerca="%".$_GET['q']."%";
							$stmt->bindParam(':q',$ricerca);
							$stmt->execute();
							$libri=$stmt->fetchAll(PDO::FETCH_ASSOC);
							echo "<table>";
							foreach($libri as $libro){
								echo "<td><img src=\"";
									echo str_replace("http://","https://",gbooks($libro["ISBN"],"copertina",urlencode($libro["Titolo"]),urlencode($libro["Autore"])));
									echo "\"></td><td>";
									echo "ISBN: ";
									echo $libro["ISBN"];
									echo "<br>Titolo: ";
									echo  $libro["Titolo"];
									echo "</br>Autore: ";
									echo  $libro["Autore"];
									echo "</br>Posizione: ";
									echo $libro["Posizione"];
									echo "<br>Stato: ".statoLibro($libro["Disponibilita"],$libro["DataPrestito"]);
									echo "</td></tr>\n";
							}
							echo "</table>";
							break;
							case 'isbn':
							if(isset($_GET['q'])){
								$isbns=$_GET["q"];
							}
							if(isset($_GET['ean'])){
								$isbns=$_GET["ean"];
							}
							$qry='SELECT * FROM Libri WHERE ISBN = :q';
							$stmt = $file_db->prepare($qry);
							$stmt->bindParam(':q',$isbns);
							$stmt->execute();
							$libri=$stmt->fetchAll(PDO::FETCH_ASSOC);
							echo "<table>";
							foreach($libri as $libro){
								echo "<td><img src=\"";
									echo str_replace("http://","https://",gbooks($libro["ISBN"],"copertina",urlencode($libro["Titolo"]),urlencode($libro["Autore"])));
									echo "\"></td><td>";
									echo "ISBN: ";
									echo $libro["ISBN"];
									echo "<br>Titolo: ";
									echo  $libro["Titolo"];
									echo "</br>Autore: ";
									echo  $libro["Autore"];
									echo "</br>Posizione: ";
									echo $libro["Posizione"];
									echo "<br>Stato: ".statoLibro($libro["Disponibilita"],$libro["DataPrestito"]);
									echo "</td></tr>\n";
							}
							echo "</table>";
							break;
							case 'posizione':
							$qry='SELECT * FROM Libri WHERE Posizione LIKE :q';
							$stmt = $file_db->prepare($qry);
							$ricerca= str_replace("*","%",$_GET['q']);
							$stmt->bindParam(':q',$ricerca);
							$stmt->execute();
							$libri=$stmt->fetchAll(PDO::FETCH_ASSOC);
							echo "<table>";
							foreach($libri as $libro){
								echo "<td><img src=\"";
									echo str_replace("http://","https://",gbooks($libro["ISBN"],"copertina",urlencode($libro["Titolo"]),urlencode($libro["Autore"])));
									echo "\"></td><td>";
									echo "ISBN: ";
									echo $libro["ISBN"];
									echo "<br>Titolo: ";
									echo  $libro["Titolo"];
									echo "</br>Autore: ";
									echo  $libro["Autore"];
									echo "</br>Posizione: ";
									echo $libro["Posizione"];
									echo "<br>Stato: ".statoLibro($libro["Disponibilita"],$libro["DataPrestito"]);
									echo "</td></tr>\n";
							}
							echo "</table>";
							break;
							case 'pos':
							$qry='SELECT DISTINCT Posizione FROM Libri ORDER BY Posizione ASC';
							$stmt = $file_db->prepare($qry);
							$ricerca="%".$_GET['q']."%";
							$stmt->execute();
							$scatole=$stmt->fetchAll(PDO::FETCH_ASSOC);
							$i=0;
							foreach ($scatole as $s){
								$qry='SELECT * FROM Libri WHERE Posizione = :q';
								$stmt = $file_db->prepare($qry);
								$stmt->bindParam(':q',$s["Posizione"]);
								$stmt->execute();
								$libri=$stmt->fetchAll(PDO::FETCH_ASSOC);
								echo "<div data-role=\"collapsible\"><h2 onclick=\"loadBlock('".strval($i)."','".strval($i+count($libri))."')\">".$s["Posizione"]."</h2><table>";
								foreach($libri as $libro){
									echo "<tr><td><img id=".strval($i)." src=\"res/vuoto.png\"> <span id=\"url".strval($i).'" class="nascosto">';
									echo "api.php?mode=copertina&titolo=".urlencode($libro["Titolo"])."&isbn=".$libro["ISBN"]."&autore=".urlencode($libro["Autore"]);
									echo '</span></td><td>';
									echo "ISBN: ";
									echo $libro["ISBN"];
									echo "<br>Titolo: ";
									echo $libro["Titolo"];
									echo "</br>Autore: ";
									echo $libro["Autore"];
									echo "<br>Stato: ".statoLibro($libro["Disponibilita"],$libro["DataPrestito"]);
									echo "</td></tr>\n";
									$i++;
								}
								echo "</table></div>";}
								break;
							case 'login':
							echo '--><h1>Login</h1><h4>'.urldecode($_GET['error']).'</h4><form method="post" action="mgr.php">
								Nome utente:
								<input type="text" name="user">
								Password:
								<input type="password" name="password">
								<input type="submit" value="Entra">
							</form>';
							break;
							default:
							break;
						}
					}
					?>
					</div>
					<div data-role="footer" data-position="fixed">	
						<div data-role="navbar" data-iconpos="top" data-theme="a">
							<ul>
								<?php
								if(isset($_GET['q'])||isset($_GET['ean'])||$_GET['mode']=="pos"){
									echo'
									<li>
									<a data-transition="fade" href="index.php" data-theme="" data-icon="bullets">
									Lista
									</a>
									</li>';
								}?>
								<li>
									<a  href="?mode=pos" data-transition="fade" data-theme="" data-icon="info">
										Lista Pos.
									</a>
								</li><?php if($_GET['mode']!='login'){echo '
								<li>
									<a ';echo 'href="';if(isset($_COOKIE['token'])){echo'mgr.php';}else{echo'#login';}echo'"';echo' data-transition="fade" data-theme="" data-icon="lock">
										Area riservata
									</a>
								</li>';}?>
							</ul>
						</div>
					</div>
				</div>
				<div data-role="page" data-control-title="Home" id="login">
					<div data-theme="a" data-role="header" data-position="fixed">
						<h3>
							Login
						</h3>
					</div>
					<div data-role="content">
						<h1>Login</h1>
						<form method="post" action="mgr.php">
							Nome utente:
							<input type="text" name="user">
							Password:
							<input type="password" name="password">
							<input type="submit" value="Entra">
						</form>
					</div>
					<div data-role="footer" data-position="fixed">	
						<div data-role="navbar" data-iconpos="top" data-theme="a">
							<ul>
								<li>
									<a data-transition="fade" href="index.php" data-theme="" data-icon="bullets">
										Lista
									</a>
								</li>
								<li>
									<a  href="?mode=pos" data-transition="fade" data-theme="" data-icon="info">
										Lista Pos.
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</body>
			</html>
