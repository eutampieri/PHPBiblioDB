<?php
function gbooks($isbn, $mode){
	$cache=json_decode(file_get_contents("pub/covers.json"),true);
	if(isset($cache[$isbn])){
		return $cache[$isbn];
	}
	else{
		if(substr($isbn,0,1)==2){
			switch($mode){
				case "copertina":
				return"res/mimg.php?id=".uniqid();
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
							$cache[$isbn]="nodata.jpg";
							file_put_contents("pub/covers.json", json_encode($cache));
							return("nodata.jpg");
						}
						else{
							$cache[$isbn]=$txturl;
							file_put_contents("pub/covers.json", json_encode($cache));
							return($txturl);
						}
					}
					$cache[$isbn]="nodata.jpg";
					file_put_contents("pub/covers.json", json_encode($cache));
					return(("nodata.jpg"));
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
							$cache[$isbn]="nodata.jpg";
							file_put_contents("pub/covers.json", json_encode($cache));
							return ("nodata.jpg");
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
echo gbooks('9788822162304','copertina');
?>