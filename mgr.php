<?php
include("res/bibliodb.php");
if(isset($_POST['user'])&&isset($_POST['password'])){
    $database = new PDO('sqlite:bibliodb.sqlite');
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $qry='SELECT * FROM Utenti WHERE Utente = :u';
    $stmt = $database->prepare($qry);
    $stmt->bindParam(':u',$_POST["user"]);
    $stmt->execute();
    $utenti=$stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($utenti)==1){
        if($utenti[0]["Master"]=="1"){
            if(password_verify($_POST["password"],$utenti[0]["Password"])){
				$qry='INSERT INTO Sessioni VALUES (:token, :ip, :scadenza, :utente)';
                $token=strval(uniqid("sess"));
                $scadenza=date("Y-m-d H:i:s",time()+860);
                $ip=$_SERVER['REMOTE_ADDR'];
				$stmt = $database->prepare($qry);
                $stmt->bindParam(':token',$token);
                $stmt->bindParam(':ip',$ip);
                $stmt->bindParam(':scadenza',$scadenza);
                $stmt->bindParam(':utente',$_POST["user"]);
				$stmt->execute();
                setcookie("token",$token, time()+860);
                header("Location: mgr.php");
            }
            else{
                header("Location: index.php?error=Password+errata");
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
$loggedIn=false;
if(isset($_COOKIE['token'])){
    $database = new PDO('sqlite:bibliodb.sqlite');
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $qry='SELECT * FROM Sessioni WHERE Token = :tk';
    $stmt = $database->prepare($qry);
    $stmt->bindParam(':tk',$_COOKIE['token']);
    $stmt->execute();
    $sess=$stmt->fetchAll(PDO::FETCH_ASSOC)[0];
    if(date_timestamp_get(date_create_from_format("Y-m-d H:i:s",$sess["Scadenza"]))>time()){
        $qry='SELECT * FROM Utenti WHERE Utente = :u';
        $stmt = $database->prepare($qry);
        $stmt->bindParam(':u',$sess["Utente"]);
        $stmt->execute();
        $role=$stmt->fetchAll(PDO::FETCH_ASSOC)[0]["Master"];
        if($role=="1"){
            if($_SERVER['REMOTE_ADDR']==$sess["IP"]){
                $loggedIn=true;
                //Prolungare la durata della sessione
            }
            else{
                header("Location: index.php?error=Login+fallito&mode=login");
            }
        }
        else{
            header("Location: index.php?error=Non+autorizzato&mode=login");
        }
    }
    else{
        header("Location: index.php?error=Sessione+scaduta&mode=login");
    }
}
else{
    header("Location: index.php#login");
    die();
}
?>
<html>
    <head>
    <meta charset="utf-8">
    <title>
        BiblioDB Manager
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- For non-Retina (@1× display) iPhone, iPod Touch, and Android 2.1+ devices: -->
    <link rel="apple-touch-icon" href="apple-touch-icon.png"><!-- 57×57px -->
    <!-- For the iPad mini and the first- and second-generation iPad (@1× display) on iOS = 6: -->
    <link rel="apple-touch-icon" sizes="72x72" href="apple-touch-icon-72x72.png">
    <!-- For the iPad mini and the first- and second-generation iPad (@1× display) on iOS = 7: -->
    <link rel="apple-touch-icon" sizes="76x76" href="apple-touch-icon-76x76.png">
    <!-- For iPhone with @2× display running iOS = 6: -->
    <link rel="apple-touch-icon" sizes="114x114" href="apple-touch-icon-114x114.png">
    <!-- For iPhone with @2× display running iOS = 7: -->
    <link rel="apple-touch-icon" sizes="120x120" href="apple-touch-icon-120x120.png">
    <!-- For iPad with @2× display running iOS = 6: -->
    <link rel="apple-touch-icon" sizes="144x144" href="apple-touch-icon-144x144.png">
    <!-- For iPad with @2× display running iOS = 7: -->
    <link rel="apple-touch-icon" sizes="152x152" href="apple-touch-icon-152x152.png">
    <!-- For iPhone 6 Plus with @3× display: -->
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon-180x180.png">
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
    <style type="text/css">
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
    </style>
    </head>
    <body>
    <div data-role="page" data-control-title="Home" id="page1"><?php if($loggedIn){ echo'
        <div data-role="panel" id="panel1" data-position="left" data-display="reveal"
        data-theme="a">
        <ul data-role="listview" data-divider-theme="h" data-inset="false">
            <li data-role="list-divider" role="heading">
                BiblioDB Manager
            </li>
                <!--li data-theme="a">
                    <a href="javascript:void(0);" data-transition="slide">
                        Aggiungi titolo
                    </a>
                </li>
                <li data-theme="a">
                    <a href="javascript:void(0);" data-transition="slide">
                        Presta titolo
                    </a>
                </li>
                <li data-theme="a">
                    <a href="javascript:void(0);" data-transition="slide">
                        Aggiungi Utente
                    </a>
                </li-->
                <li data-theme="a">
                    <a href="?mode=scatola" data-transition="slide">
                        Sposta posizione
                    </a>
                </li>
                <li data-theme="a">
                    <a href="?mode=modifica" data-transition="slide">
                        Modifica voce
                    </a>
                </li>
                <li data-theme="a">
                    <a href="?mode=lbif" data-transition="slide">
                        Importazione file LBIF
                    </a>
                </li>
                <li data-theme="a">
                    <a href="logout.php" data-transition="slide">
                        Esci
                    </a>
                </li>
            </ul>
        </div>';}?>
        <div data-theme="a" data-role="header" data-position="fixed"><?php if($loggedIn){ echo'
            <div id="menubutton">
                <a data-controltype="panelbutton" data-role="button" href="#panel1" data-icon="bars" data-iconpos="left" class="ui-btn-left">
                    Menù
                </a>
            </div>';}?>
        <h3>
            BiblioDB Manager
        </h3>
        </div>
        <div data-role="content">
        <?php
        $database = new PDO('sqlite:bibliodb.sqlite');
        if(isset($_POST['mode'])&&$_POST['mode']=='edit'){
            $id=$_POST['id'];
            $qry='UPDATE Libri SET Titolo = :tit, Autore = :aut, Posizione=:pos WHERE ID = :id';
            $stmt = $database->prepare($qry);
            $stmt->bindParam(':id',$id);
            $stmt->bindParam(':tit',$_POST['tit']);
            $stmt->bindParam(':aut',$_POST['aut']);
            $stmt->bindParam(':pos',$_POST['pos']);
            $stmt->execute();
            echo "<h3>Aggiornato ".$_POST['tit'].'</h3>';
        }
        else if(isset($_POST['mode'])&&$_POST['mode']=='add'){
            $qry='INSERT INTO Libri (ID, ISBN, Titolo, Autore, Posizione, Proprietario) VALUES(:id, :isbn, :tit, :aut, :pos, "Biblioteca")';
            $stmt = $database->prepare($qry);
            $id=strval(uniqid("libro"));
            $stmt->bindParam(':id',$id);
            $stmt->bindParam(':tit',$_POST['tit']);
            $stmt->bindParam(':aut',$_POST['aut']);
            $stmt->bindParam(':pos',$_POST['pos']);
            $stmt->bindParam(':isbn',$_POST['isbn']);
            $stmt->execute();
            echo "<h3>Aggiunto ".$_POST['tit'].'</h3>';
        }
        if(isset($_GET['mode'])){
            switch($_GET['mode']){
            case 'scatola':
                echo '<form method="post" action="?mode=scatola">Origine:<div data-role="fieldcontain" data-controltype="selectmenu">
                    <select name="s">
                    ';
                $qry='SELECT DISTINCT Posizione FROM Libri ORDER BY Posizione ASC';
                $stmt = $database->prepare($qry);
                $stmt->execute();
                $lscatole=$stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($lscatole as $s){
                    $s=$s["Posizione"];
                    if(isset($_POST['s'])&&isset($_POST['d'])&&$s==$_POST['s']){
                        echo '<option value="'.$_POST['d'].'">'.$_POST['d'].'</option>\n';
                    }
                    else{
                        echo '<option value="'.$s.'">'.$s."</option>\n";
                    }
                }
                echo'
                    </select>
                    </div>Destinazione: 
                    <input type="text" name="d">
                    <input type="hidden" name="mode" value="scatola">
                    <input type="submit" value="Sposta"></form>';
                if(isset($_POST['s'])&&isset($_POST['d'])){
                    echo "<table>";
                    $qry='UPDATE Libri SET Posizione = :d WHERE Posizione = :s';
                    $stmt = $database->prepare($qry);
                    $stmt->bindParam(':d',$_POST['d']);
                    $stmt->bindParam(':s',$_POST['s']);
                    $stmt->execute();
                    $qry='SELECT * FROM Libri WHERE Posizione=:d';
                    $stmt = $database->prepare($qry);
                    $stmt->bindParam(':d',$_POST['d']);
                    $stmt->execute();
                    $libriSpostati=$stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($libriSpostati as $libro) {
                        echo "<td><img src=\"";
                        echo gbooks($libro["ISBN"],"copertina",urlencode($libro["Titolo"]),urlencode($libro["Autore"]));
                        echo '"></td><td>';
                        echo "ISBN: ";
                        echo $libro["ISBN"];
                        echo "<br>Titolo: ";
                        echo $libro["Titolo"];
                        echo "</br>Autore: ";
                        echo $libro["Autore"];
                        echo "<br>Posizione precedente: ".$_POST['s'];
                        echo "<br>Nuova posizione: ".$_POST['d'];
                        echo "</td></tr>\n";
                    }
                echo "</table>";
                }
                break;
            case 'add':
                break;
            case 'elimina':
                $i=$_GET['id'];
                $qry='SELECT Titolo FROM Libri WHERE ID=:d';
                $stmt = $database->prepare($qry);
                $stmt->bindParam(':d',$_GET['id']);
                $stmt->execute();
                $tit=$stmt->fetchAll(PDO::FETCH_ASSOC)[0]["Titolo"];
                $qry='DELETE FROM Libri WHERE ID=:d';
                $stmt = $database->prepare($qry);
                $stmt->bindParam(':d',$_GET['id']);
                $stmt->execute();
                echo "<h2>Rimosso ".$tit."</h2>";
                break;
            case 'modifica':
                if(isset($_GET['id'])){
                    $qry='SELECT * FROM Libri WHERE ID=:d';
                    $stmt = $database->prepare($qry);
                    $stmt->bindParam(':d',$_GET['id']);
                    $stmt->execute();
                    $libro=$stmt->fetchAll(PDO::FETCH_ASSOC)[0];
                    $key=$_GET['isbn'];
                    echo "<form method=\"post\" action=\"mgr.php\"><input type=\"hidden\" name=\"mode\" value=\"edit\">";
                    echo "<img src=\"";
                    echo gbooks($libro["ISBN"],"copertina",urlencode($libro["Titolo"]),urlencode($libro["Autore"])).'">';
                    echo "<br>Titolo: ";
                    echo '<input type="text" name="tit" value="';
                    echo $libro["Titolo"];
                    echo '">';
                    echo "</br>Autore: ";
                    echo '<input type="text" name="aut" value="';
                    echo $libro["Autore"];
                    echo '">';
		            echo '<input type="hidden" name="id" value="'.$_GET['id'].'">';
                    echo "<br>Posizione: ";
                    echo '<input type="text" name="pos" value="';
                    echo $libro["Posizione"];
                    echo '">';
                    $libri[1][$key]=$_POST['d'];
                    echo "<input type=\"submit\" value=\"Salva\"></form>\n";
                }
                elseif(isset($_GET['pos'])){
                    echo "<table>";
                    $qry='SELECT * FROM Libri WHERE Posizione=:d';
                    $stmt = $database->prepare($qry);
                    $stmt->bindParam(':d',$_GET['pos']);
                    $stmt->execute();
                    $libri=$stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($libri as $libro) {
                        echo "<td><img src=\"";
                        echo gbooks($libro["ISBN"],"copertina",urlencode($libro["Titolo"]),urlencode($libro["Autore"]));
                        echo '"></td><td>';
                        echo "Titolo: ";
                        echo $libro["Titolo"];
                        echo "</br>Autore: ";
                        echo $libro["Autore"];
                        echo "<br>ISBN: ";
                        echo $libro["ISBN"];
                        echo "<br>Posizione: ".$libro["Posizione"];
                        echo '</td><td><a href="?mode=modifica&id='.$libro["ID"].'"><img src="edit.svg"></a></td><td><a href="?mode=elimina&id='.$libro["ID"].'"><img src="del.svg"></a></td></tr>'."\n";
                    }
                    echo "</table>";
                }
                else{
                    echo "<h1>Seleziona la posizione</h1>";
                    $qry='SELECT DISTINCT Posizione FROM Libri ORDER BY Posizione ASC';
                    $stmt = $database->prepare($qry);
                    $stmt->execute();
                    $lscatole=$stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach($lscatole as $s){
                        $s=$s["Posizione"];
                        if($s==""){
                            echo '<a href="?mode=modifica&pos=" class="ui-btn">Nessuna posizione</a>'."\n";
                        }
                        else{
                            echo '<a href="?mode=modifica&pos='.urlencode($s).'" class="ui-btn">'.$s.'</a>'."\n";
                        }
                    }
                }
                break;
            case "lbif":
                //Importazione File LBIF
                if(isset($_POST["mode"])&&$_POST["mode"]=="lbifUpload"){
                    //Elabora LBIF
                    echo "<h1>Importazione LBIF</h1><h2>Libri aggiunti:\n<ul>";
                    $lbif=json_decode(file_get_contents($_FILES["db"]["tmp_name"]));
                    foreach($lbif as $isbn, $dati){
                        $tit=$dati[0];
                        $aut=$dati[1];
                        $pos=$dati[2];
                        $qry="INSERT INTO Libri VALUES (:id, :isbn, :titolo, :aut, :pos, :disp, :dp, :own)";
				        $stmt = $file_db->prepare($qry);
				        $stmt->bindParam(':id',strval(uniqid("libro")));
				        $stmt->bindParam(':isbn',$isbn);
				        $stmt->bindParam(':titolo',$tit);
				        $stmt->bindParam(':aut',$aut);
				        $stmt->bindParam(':pos',$pos);
				        $stmt->execute();
                        echo "<li>Aggiunto ".$tit.", di ".$aut."</li>\n";
                    }
                    echo "</li>";
                }
                else{
                    echo '<h1>Carica il database dei libri</h1>
		            <form action="mgr.php?mode=lbif" method="POST" enctype="multipart/form-data">
			        <input type="hidden" name="mode" value="lbifUpload">
			        Carica il file .lbif:<input type="file" name="db">
			        <input type="submit" value="Carica">
		            </form>';
                }
                break;
            default:
                break;
            }
        }
        else{
            echo '<h1>Console di amministrazione BiblioDB</h1>';
            if(!$loggedIn){
            echo'
                <div data-role="fieldcontain" data-controltype="textinput">
                <label for="textinput4">
                Nome Utente
                </label>
                <input name="" id="textinput4" placeholder="" value="" type="text">
                </div>
                <div data-role="fieldcontain" data-controltype="textinput">
                <label for="textinput3">
                Password
                </label>
                <input name="" id="textinput3" placeholder="" value="" type="password"> 
                </div>
                <a data-role="button" onclick="login()">
                Accedi
                </a>';
            }
        }
        ?>
        </div>
        <div data-role="footer" data-position="fixed">  
        <div data-role="navbar" data-iconpos="top" data-theme="a">
            <ul>
            <?php
            if(isset($_COOKIE['q'])||isset($_COOKIE['ean'])||$_GET['mode']=="pos"){
                echo'
                        <li>
                        <a data-transition="fade" href="index.php" data-theme="" data-icon="bullets">
                        Lista
                        </a>
                        </li>';
            }?>
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
