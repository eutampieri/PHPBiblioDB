<?php
function ETgetHN(){
	$hostname = gethostname();
	if(!$hostname) $hostname = php_uname('n');
	if(!$hostname) $hostname = getenv('HOSTNAME'); 
	if(!$hostname) $hostname = trim(`hostname`); 
	if(!$hostname) $hostname = exec('echo $HOSTNAME');
	if(!$hostname) $hostname = preg_replace('#^\w+\s+(\w+).*$#', '$1', exec('uname -a'));
	return $hostname;
}
function get_http_response_code($domain1)
{
	$headers = get_headers($domain1);
	return substr($headers[0], 9, 3);
}

function dataIT($data)
{
	$data = split(" ", $data) [0];
	$data = split("-", $data);
	return $data[2] . "/" . $data[1] . "/" . $data[0];
}

function statoLibro($stato, $data)
{
	if ($stato != NULL) {
		if ($stato) {
			return "Disponibile";
		}
		else {
			return "Prestato in data " . dataIT($data);
		}
	}
	else {
		return "Disponibile";
	}
}

function gbooks($isbn, $mode, $tit, $aut)
{
	$cache = json_decode(file_get_contents("covers.json") , true);
	if (isset($cache[$isbn]) && $mode == "copertina") {
		return $cache[$isbn];
	}
	else {
		if (substr($isbn, 0, 1) == 2) {
			switch ($mode) {
			case "copertina":
				return "res/mimg.php?tit=" . $tit . "&aut=" . $aut;
			case "titolo":
				return "Nessun dato";
			case "autore":
				return "Nessun dato";
			}
		}
		else {
			$result = json_decode(file_get_contents("https://www.googleapis.com/books/v1/volumes?q=isbn:" . $isbn . "&projection=lite") , true);
			if ($result["totalItems"] > 0) {
				$info = $result["items"][0]["volumeInfo"];
				if ($mode == "copertina") {
					if (isset($info["imageLinks"]["thumbnail"])) {
						return ($info["imageLinks"]["thumbnail"]);
					}
					else {
						$txturl = "http://img.libraccio.it/images/" . $isbn . "_0_200_0_100.jpg";
						return $txturl;
						if (get_http_response_code($txturl) != "200") {
							$cache[$isbn] = "res/mimg.php?tit=" . $tit . "&aut=" . $aut;
							file_put_contents("covers.json", json_encode($cache));
							return "res/mimg.php?tit=" . $tit . "&aut=" . $aut;
						}
						else {
							$cache[$isbn] = $txturl;
							file_put_contents("covers.json", json_encode($cache));
							return ($txturl);
						}
					}

					$cache[$isbn] = "res/mimg.php?tit=" . $tit . "&aut=" . $aut;
					file_put_contents("covers.json", json_encode($cache));
					return "res/mimg.php?tit=" . $tit . "&aut=" . $aut;
				}
				else {
					if ($mode == "titolo") {
						return $info["title"];
					}
					else
					if ($mode == "autore") {
						return $info["authors"];
					}
				}
			}
			else {
				if ($mode == "copertina") {
					$txturl = "http://img.libraccio.it/images/" . $isbn . "_0_200_0_100.jpg";
					if (get_http_response_code($txturl) != "200" || file_get_contents("http://img.libraccio.it/images/N00030000_0_200_0_100.jpg") == file_get_contents($txturl)) {
						$sb = exec('python sbcover.py ' . $isbn);
						if (get_http_response_code($sb) != 200) {
							$cache[$isbn] = "res/mimg.php?tit=" . $tit . "&aut=" . $aut;
							file_put_contents("covers.json", json_encode($cache));
							return "res/mimg.php?tit=" . $tit . "&aut=" . $aut;
						}
						else {
							$cache[$isbn] = $sb;
							file_put_contents("covers.json", json_encode($cache));
							return $sb;
						}
					}
					else {
						$cache[$isbn] = $txturl;
						file_put_contents("covers.json", json_encode($cache));
						return ($txturl);
					}
				}
				else {
					return ("Nessun dato");
				}
			}
		}
	}
}
function checkDigitEAN13($ean){
	$ean=strval($ean);
	$count=1;
	$chkn=0;
	foreach(str_split($ean) as $c){
		if ($count%2==0){
			$chkn=$chkn+intval($c)*3;
		}
		else{
			$chkn=$chkn+intval($c)*1;
		}
		$count=$count+1;
	}
	if ($chkn%10==0){
		$chkn=0;
	}
	else{
		$chkn=10-$chkn%10;
	}
	return $ean.strval($chkn);
}