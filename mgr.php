<?php
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
                $token=setToken($_POST["user"],$_POST["password"],800);
                setcookie("token",$token, time()+860);
                header("Location: mgr.php");
            }
            else{
                header("Location: index.php?error=Password+errata+".hash("sha256",$_POST["password"]).'+'.$utenti[0]["Password"]."&mode=login");
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
}/*
$loggedIn=false;
if(isset($_COOKIE['token'])){
    $u=getToken($_COOKIE['token'])['user'];
    $p=getToken($_COOKIE['token'])['pwd'];
    if(UTENTE_ESISTENTE){
        if(AMMINISTRATORE){
            if(PASSWORD_OK){
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
        function get_http_response_code($domain1) {
            $headers = get_headers($domain1);
            return substr($headers[0], 9, 3);
        }
        function statoLibro($isbn,$dict){
            if(isset($dict[0][$isbn])){
            if($dict[0][$isbn]==1){
                return "Disponibile";
            }
            else{
                return "Prestato in data ".$dict[8][$isbn];
            }
            }
            else{
            return "Disponibile";
            }
        }
        function gbooks($isbn, $mode,$tit,$aut){
            $cache=json_decode(file_get_contents("pub/covers.json"),true);
            if(isset($cache[$isbn])){
            return $cache[$isbn];
            }
            else{
            if(substr($isbn,0,1)==2){
                switch($mode){
                case "copertina":
                    return"res/mimg.php?id=".uniqid()."&tit=".$tit."&aut=".$aut;
                case "titolo":
                    return "Nessun dato";
                case "autore":
                    return "Nessun dato";
                }
            }
            else{
                $result=json_decode(file_get_contents("https://www.googleapis.com/books/v1/volumes?q=isbn:".$isbn."&projection=lite"),true);
                if($result["totalItems"]>0){
                $info=$result["items"][0]["volumeInfo"];
                if($mode=="copertina"){
                    if(isset($info["imageLinks"]["thumbnail"])) {
                    return($info["imageLinks"]["thumbnail"]);
                    }
                    else {
                    $txturl="http://img.libraccio.it/images/".$isbn."_0_200_0_100.jpg";
                    return $txturl;
                    if(get_http_response_code($txturl) != "200"){
                        $cache[$isbn]="res/mimg.php?id=".uniqid()."&tit=".$tit."&aut=".$aut;
                        file_put_contents("pub/covers.json", json_encode($cache));
                        return"res/mimg.php?id=".uniqid()."&tit=".$tit."&aut=".$aut;
                    }
                    else{
                        $cache[$isbn]=$txturl;
                        file_put_contents("pub/covers.json", json_encode($cache));
                        return($txturl);
                    }
                    }
                    $cache[$isbn]="res/mimg.php?id=".uniqid()."&tit=".$tit."&aut=".$aut;
                    file_put_contents("pub/covers.json", json_encode($cache));
                    return"res/mimg.php?id=".uniqid()."&tit=".$tit."&aut=".$aut;
                }
                else{
                    if($mode=="titolo"){
                    return $info["title"];
                    }
                    else if($mode=="autore"){
                    return $info["authors"];
                    }
                }
                }
                else{
                if($mode=="copertina"){
                    $txturl="http://img.libraccio.it/images/".$isbn."_0_200_0_100.jpg";
                    if(get_http_response_code($txturl) != "200"||file_get_contents("http://img.libraccio.it/images/N00030000_0_200_0_100.jpg")==file_get_contents($txturl)){
                    $sb=exec('python sbcover.py '.$isbn);
                    if(get_http_response_code($sb)!=200){
                        $cache[$isbn]="res/mimg.php?id=".uniqid()."&tit=".$tit."&aut=".$aut;
                        file_put_contents("pub/covers.json", json_encode($cache));
                        return"res/mimg.php?id=".uniqid()."&tit=".$tit."&aut=".$aut;
                    }
                    else{
                        $cache[$isbn]=$sb;
                        file_put_contents("pub/covers.json", json_encode($cache));
                        return $sb;
                    }
                    }
                    else{
                    $cache[$isbn]=$txturl;
                    file_put_contents("pub/covers.json", json_encode($cache));
                    return ($txturl);
                    }
                }
                else{
                    return("Nessun dato");
                }
                }
            }
            }
        }
        function presta($isbn,$state,$user){
			$tessere=json_decode(file_get_contents('tessere.json'),true);
			if(isset($tessere[0][$user])){
				#
			}
			else{
				return "Utente insesistente";
			}
		}
        $libri=json_decode(file_get_contents("bibliodb.json"),true);
        $lscatole=array();
        foreach ($libri[1] as $key => $value){
            if(!isset($lscatole[$value])){
            $lscatole[$value]=0;
            }
        }
        if(isset($_POST['mode'])&&($_POST['mode']=='add'||$_POST['mode']=='edit')){
            $i=$_POST['isbn'];
            $libri[1][$i]=strtoupper($_POST['pos']);
            $libri[2][strtolower($_POST['tit'])]=$i;
            $libri[3][$i]=strtolower($_POST['tit']);
            $libri[4][$i]=strtolower($_POST['aut']);
            $libri[6][$i]="Biblioteca";
            file_put_contents('bibliodb.json', json_encode($libri));
            if($_POST['mode']=='edit'){
                echo "<h3>Aggiornato ".ucwords($_POST['tit']).'</h3>';
            }
            elseif($_POST['mode']=='add'){
                echo "<h3>Aggiunto ".ucwords($_POST['tit']).'</h3>';
            }
        }
        if(isset($_GET['mode'])){
            switch($_GET['mode']){
            case 'scatola':
                echo '<form method="post" action="?mode=scatola">Origine:<div data-role="fieldcontain" data-controltype="selectmenu">
                    <select name="s">
                    ';
                foreach ($lscatole as $s=>$key){
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
                foreach ($libri[1] as $key => $value) {
                    if($value==$_POST['s']){
                    echo "<td><img src=\"";
                    echo gbooks($key,"copertina",urlencode(ucwords($libri[3][$key])),urlencode(ucwords($libri[4][$key])));
                    echo '"></td><td>';
                    echo "ISBN: ";
                    echo $key;
                    echo "<br>Titolo: ";
                    echo ucwords($libri[3][$key]);
                    echo "</br>Autore: ";
                    echo ucwords($libri[4][$key]);
                    echo "<br>Posizione: ".$value;
                    echo "<br>Nuova posizione: ".$_POST['d'];
                    $libri[1][$key]=$_POST['d'];
                    echo "</td></tr>\n";
                    }
                }
                echo "</table>";
                file_put_contents('bibliodb.json',json_encode($libri));
                }
                break;
            case 'add':

            break;
            case 'elimina':
            $i=$GET['isbn'];
            unset($libri[1][$i]);
            unset($libri[2][$libri[3][$i]]);
            unset($libri[3][$i]);
            unset($libri[4][$i]);
            unset($libri[6][$i]);
            file_put_contents('bibliodb.json', json_encode($libri));
            echo "<h2>Rimosso ".$i."</h2>";
            break;
            case 'modifica':
                if(isset($_GET['isbn'])){
                    $key=$_GET['isbn'];
                    echo "<form method=\"post\" action=\"mgr.php\"><input type=\"hidden\" name=\"mode\" value=\"edit\">";
                    echo "<img src=\"";
                    echo gbooks($key,"copertina",urlencode(ucwords($libri[3][$key])),urlencode(ucwords($libri[4][$key]))).'">';
                    echo "<br>Titolo: ";
                    echo '<input type="text" name="tit" value="';
                    echo ucwords($libri[3][$key]);
                    echo '">';
                    echo "</br>Autore: ";
                    echo '<input type="text" name="aut" value="';
                    echo ucwords($libri[4][$key]);
                    echo '">';
		    echo '<input type="hidden" name="isbn" value="'.$key.'">';
                    echo "<br>Posizione: ";
                    echo '<input type="text" name="pos" value="';
                    echo ucwords($libri[1][$key]);
                    echo '">';
                    $libri[1][$key]=$_POST['d'];
                    echo "<input type=\"submit\" value=\"Salva\"></form>\n";
                }
                elseif(isset($_GET['pos'])){
                    echo "<table>";
                    foreach ($libri[3] as $isbn => $titolo) {
                        if($libri[1][$isbn]==$_GET['pos']){
                        echo "<td><img src=\"";
                        echo gbooks($isbn,"copertina",urlencode(ucwords($titolo)),urlencode(ucwords($libri[4][$isbn])));
                        echo '"></td><td>';
                        echo "Titolo: ";
                        echo ucwords($titolo);
                        echo "</br>Autore: ";
                        echo ucwords($libri[4][$isbn]);
                        echo "<br>ISBN: ";
                        echo $isbn;
                        echo "<br>Posizione: ".$libri[1][$isbn];
                        echo '</td><td><a href="?mode=modifica&isbn='.$isbn.'"><img src="edit.svg"></a></td><td><a href="?mode=elimina&isbn='.$isbn.'"><img src="del.svg"></a></td></tr>'."\n";}
                    }
                    echo "</table>";
                }
                else{
                    echo "<h1>Seleziona la posizione</h1>";
                    foreach($lscatole as $s=>$i){
                        if($s==""){
                            echo '<a href="?mode=modifica&pos=" class="ui-btn">Nessuna posizione</a>'."\n";
                        }
                        else{
                            echo '<a href="?mode=modifica&pos='.urlencode($s).'" class="ui-btn">'.$s.'</a>'."\n";
                        }
                    }
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
