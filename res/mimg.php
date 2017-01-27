<?php
$cornici= array(0 => '',1=>'qd.png',2=>'ro.png');
$c=$cornici[rand(0,2)];
$covers=array(0 => 'blue.jpg',1=>'sky.jpg',2=>'green.jpg',3=>'red.jpg',4=>'violet.jpg');
$dest = imagecreatefromjpeg($covers[rand(0,4)]);

if($c!=''){
	$src = imagecreatefrompng($c);
	$new_im = imagecreatetruecolor(200,290);
	imagecolortransparent($new_im, imagecolorallocate($new_im, 0, 0, 0));
	imagecopyresampled($new_im,$src,0,0,0,0,200,290,200,290);
	imagecopymerge($dest, $new_im, 0, 0, 0, 0, 200, 290, 100);
}

header('Content-Type: image/png');
$color = imagecolorallocate($dest, 255, 255, 255);
$tit=urldecode($_GET['tit']);
if(strlen($tit)<15){
	$nos=(15-strlen($tit))/2;
	for($s=0;$s<$nos;$s++){
		$tit=" ".$tit." ";
	}
}
elseif(strlen($tit)>15){
	$tit=substr($tit, 0,12)."...";
}
imagestring($dest,5,30,60,$tit,$color);
$aut=urldecode($_GET['aut']);
if(strlen($aut)<15){
	$nos=(15-strlen($aut))/2;
	for($s=0;$s<$nos;$s++){
		$aut=" ".$aut." ";
	}
}
// elseif(strlen($aut)>15){
// 	$aut=explode(" ", $aut)[1];
// }
// if(strlen($aut)<15){
// 	$nos=(15-strlen($aut))/2;
// 	for($s=0;$s<$nos;$s++){
// 		$aut=" ".$aut." ";
// 	}
// }
elseif(strlen($aut)>15){
	$aut=substr($aut, 0,12)."...";
}
imagestring($dest,5,30,240,$aut,$color);
imagepng($dest);

imagedestroy($dest);
imagedestroy($src);
?>