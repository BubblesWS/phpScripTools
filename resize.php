<?php
#######################################################
### PHP FUNCTION for resizing images
### Author: Riccardo Sadocchi - Bubbles WebSolution
### Email: bubbles.websolution@gmail.com
### License:  GNU General Public License
#######################################################
#######################################################
/*
	The ONLY required parameter is the 1st.
	You can write 'T' for ALL images in the folder, or the image name (like 'flower.jpg')

	resize('image name or T', 'source folder', 'destination folder' 'max width', 'max heigth', 'new name', 'overwrite');
	Legend:
		'image name or T' (!->required) : string
			if you write T the script resize all the image of the source folder

		'source folder' (!->optional) : string
			by default the source folder was set in the same where the script run

		'destination folder' (!->optional) : string
			by default the script make a new folder 'resized' inside the source

		'max width' & 'max heigth' (!->optional) : int
			by default if the original size it's more then 1000px, the script set the max dimension at 1/3 of original,
			if original are less or like 1000px, the script set the max at 1/2

		'new name' (!->optional) : string
			by default do not change the name

		'overwrite' (!->optional) : false or true
			by default is false, if in the destination folder exist a picture whit same name, exit

	This example set only the max dimension and the overwrite:
		resize('my-image.png',,,500,500,,true);
	This example set the folders and the max dim, and skip the name and the overwrite:
		resize('my-image.png','/gallery/','/gallery/mobile/',500,500,);
*/
//
//
//
################################################## RESIZE FUNCTION ##################################################
function resize($IMG, $SRC="", $DST="", $MAXw=null, $MAXh=null, $IMGr="", $OVERWRITE=false){
	/* ***** Allowed type: PNG JPG JPEG GIF ***** */
	//
	//
	// check and/or define the source and destination folder
	if(!$SRC || $SRC = ""){
		$SRC = setSRC();
	}
	else{
		PathCheck($SRC);
	}
	//
	if(!$DST || $DST = ""){
		$DST = $SRC."resized/";
	}
	else{
		PathCheck($DST);
	}
	//
	// check if is defined one or all image inside the source folder
	if(!$IMG || $IMG = "") die("<h1>Select a image or &quot;T&quot; for all image in the source path!!!</h1>");
	//
	elseif($IMG == "T"){
		MULTI_RESIZE($SRC,$DST,$MAXw,$MAXh,$IMGr,$OVERWRITE);
	}
	//
	else{
		if(!$MAXw && !$MAXh){
			$res = setMAXDIM($SRC,$IMG);
			if($res['MaxWidth'] > $MAXw && $res['MaxHeight'] > $MAXh){
				$MAXw = $res['MaxWidth'];
				$MAXh = $res['MaxHeight'];
			}
		}
		elseif(!$MAXw xor !$MAXh){
			if(!$MAXw){
				$MAXw = $MAXh;
			}
			elseif(!$MAXh){
				$MAXh = $MAXw;
			}
		}
		//
		if(!$IMGr){
			$IMGr = $IMG;
		}
		else{
			$IMGr = SINGLE_RENAME($IMGr,$IMG);
		}
		//
		SINGLE_RESIZE($IMG,$SRC,$DST,$MAXw,$MAXh,$IMGr,$OVERWRITE);
	}
	//
	//
	//
	function MULTI_RESIZE($source,$dest,$maxw=null,$maxh=null,$imgr="",$overwrite=false){
		$handle = PathCheck($source);
		$images = array();
		while(false !== ($image = readdir($handle))){
			if(strstr(strtolower($image),".png") || strstr(strtolower($image),".jpg") || strstr(strtolower($image),".jpeg") || strstr(strtolower($image),".gif")){
				$images[] = $image;
			}
		}
		sort($images);
		//
		if(!$maxw && !$maxh){
			foreach($images as $k){
				$res = setMAXDIM($source,$k);
				if($res['MaxWidth'] > $maxw && $res['MaxHeight'] > $maxh){
					$maxw = $res['MaxWidth'];
					$maxh = $res['MaxHeight'];
				}
			}
		}
		elseif(!$maxw xor !$maxh){
			if($!$maxw){
				$maxw = $maxh;
			}
			elseif(!$maxh){
				$maxh = $maxw;
			}
		}
		//
		if($imgr){
			MULTI_RENAME($imgr,$images);
		}
		else{
			$imagesr = array();
			$imagesr = $images;
		}
		//
		$tt = count($images);
		for($x=0;$x<$tt;$x++){
   			if($overwrite){
   				list($width,$height) = getimagesize($source.$images[$x]);
				if (($width>$maxw) || ($height>$maxh)) SINGLE_RESIZE($images[$x],$source,$dest,$maxw,$maxh,$imagesr[$x],true);
				else copy($source.$images[$x],$dest.$imagesr[$x]);
   			}
   			else{
   				if(!glob($dest.$imagesr[$x])){
   					list($width,$height) = getimagesize($source.$images[$x]);
					if (($width>$maxw) || ($height>$maxh)) SINGLE_RESIZE($images[$x],$source,$dest,$maxw,$maxh,$imagesr[$x],false);
					else copy($source.$images[$x],$dest.$imagesr[$x]);
   				}
   			}
   		}
	}
	//
	function SINGLE_RESIZE($img,$source,$dest,$maxw,$maxh,$imgr,$overwrite=false){
		list($srcW, $srcH) = getimagesize($source.$img);
		if ($srcW < $srcH){
			$dstW = ($maxh/$srcH)*$srcW;
			$dstH = $maxh;
		}
		elseif ($srcW>$srcH){
			$dstH = ($maxw/$srcW)*$srcH;
			$dstW = $maxw;
		}
		else{
			$dstW = $maxw;
			$dstH = $maxh;
		}
		//
		$temp = imagecreatetruecolor($dstW, $dstH);
		if (substr(strtolower($img), ".png")){
			$picture = imagecreatefrompng($source.$img);
			imagecopyresampled($temp, $picture, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
			imagepng($temp, $dest.$imgr);
		}
		elseif (substr(strtolower($img), ".jpg") || substr(strtolower($img), ".jpeg")){
			$picture = imagecreatefromjpeg($source.$img);
			imagecopyresampled($temp, $picture, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
			imagejpeg($temp, $dest.$imgr);
		}
		elseif (substr(strtolower($img), ".gif")){
			$picture = imagecreatefromgif($source.$img);
			imagecopyresampled($temp, $picture, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
			imagegif($temp, $dest.$imgr);
		}
	}
	//
	//
	//
	// ************************************************** //
	// other function
	//
	function ALLOWED($f=$IMG){
		$allowed = array('.png','.jpg','.jpeg','.gif');
    $split = split('.', strtolower($f));
    $n = count($split);
    if(!in_array($split[$n--], $allowed))){ return false; }
		else{ return true; }
	}
	//
	function setSRC(){
		$pos = $_SERVER['SCRIPT_NAME'];
		$t = strlen($pos);
		$p = 1;
		for($i = 1; $i < $t; $i++){ if(strpos($pos,"/",$i)){ $p++; } }
		$src = substr($pos,0,$p);
		return $src;
	}
	//
	function PathCheck($path){
		if(substr($path,-1) != "/"){ $path = $path."/"; }
		return $path;
	}
	//
	function setMAXDIM($p=$SRC,$f=$IMG){
		list($w, $h) = getimagesize($p.$f);
    if($w > 1000 && $h > 1000){
			$w /= 3;
    	$h /= 3;
      }
    elseif($w <= 1000 xor $h <= 1000){
    	$w /= 2;
    	$h /= 2;
    }
    $dim = array("MaxWidth"=>$w,"MaxHeight"=>$h);
    return $dim;
	}
	//
	function SINGLE_RENAME($name,$img){
		$splitN = split('.',$name);
		$splitI = split('.',$img);
		$nN = count($splitN);
		$nI = count($splitI);
		$extN = ".".$splitN[$nsplitN--];
		$extI = ".".$splitI[$nsplitI--];
		if($extN == $extI){
			$newname = $name;
		}
		else{
			if($nsplitN > 2){
				unset($splitN[$nsplitN--]);
				$newname = implode('.', $splitN).$extI;
			}
			else{
				$newname = $splitN[0].$extI;
			}
		}
		return $newname;
	}
	//
	function MULTI_RENAME($nm,$imgs){
		$tot = count($imgs);
		$nms = array();
		$ext = "";
		if(strpos($nm,'.',-4) || strpos($nm,'.',-5)){
			$split = split('.', $nm);
			$nsplit = count($split);
			$ext = ".".$split[$nsplit--];
			unset($split[$nsplit--]);
			$nm = implode('.', $split);
		}
		if($tot > 9){
			for($p=0;$p==8;$p++){
				$nms[] = "00".$p."-".$nm.$ext;
			}
			for($p=9;$p<$tot;$p++){
				$nms[] = "0".$p."-".$nm.$ext;
			}
		}
		else{
			for($p=0;$p<$tot;$p++){
				$nms[] = "00".$p."-".$nm.$ext;
			}
		}
		return $nms;
	}
}
?>
