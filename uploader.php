
<?php
include ('../../../wp-config.php');

$my_name = 'mytest';
$u = $_GET["u"];
$p = $_GET["p"];
////start of database implementation
$time = time();
$fname=$u."-".$time."-".$t."-" . $_FILES["file"]["name"];

/// check if user & pass exist in DataBase
 if ( ($u == get_option('idump_username'))&&($p == get_option('idump_password'))||($u == get_option('idump_username1'))&&($p == get_option('idump_password1')) ) 

{

      $query = "INSERT INTO `iphoto` (`username`,`file`,`date`) 
      VALUES ('$u','$fname','$time')";
      $result = mysql_query ($query) 

  or die("Query error: ". mysql_error()); 
}
else
echo"";

/// end of database implementation////

/////Crop image////

function cropImage($nw, $nh, $source, $stype, $dest) {
    $size = getimagesize($source);
    $w = $size[0];
    $h = $size[1];
 
    switch($stype) {
        case 'gif':
        $simg = imagecreatefromgif($source);
        break;
        case 'jpg':
        $simg = imagecreatefromjpeg($source);
        break;
        case 'png':
        $simg = imagecreatefrompng($source);
        break;
    }
 
    $dimg = imagecreatetruecolor($nw, $nh);
 
    $wm = $w/$nw;
    $hm = $h/$nh;
 
    $h_height = $nh/2;
    $w_height = $nw/2;
 
    if($w> $h) {
 
        $adjusted_width = $w / $hm;
        $half_width = $adjusted_width / 2;
        $int_width = $half_width - $w_height;
 
        imagecopyresampled($dimg,$simg,-$int_width,0,0,0,$adjusted_width,$nh,$w,$h);
 
    } elseif(($w <$h) || ($w == $h)) {
 
        $adjusted_height = $h / $wm;
        $half_height = $adjusted_height / 2;
        $int_height = $half_height - $h_height;
 
        imagecopyresampled($dimg,$simg,0,-$int_height,0,0,$nw,$adjusted_height,$w,$h);
    } else {
       imagecopyresampled($dimg,$simg,0,0,0,0,$nw,$nh,$w,$h);
    }
    imagejpeg($dimg,$dest,100);

}

/////cropimamge end -- 
//upload form
 if ( ($u == get_option('idump_username'))&&($p == get_option('idump_password'))||($u == get_option('idump_username1'))&&($p == get_option('idump_password1')) ) 
 {
 
	
echo"<div><form action=\"uploader.php\"  method=\"post\" enctype=\"multipart/form-data\">
	<input type=\"file\" name=\"file\" id=\"file\" ><br>
	<input type=\"submit\" name=\"submit\" value=\"Upload\" >
	</form></div> ";


	
$path = "files";
$path2 = "files/thumb";
//$dh = opendir($path2);

//closedir($dh);		
  
if ($_FILES["file"]["error"] > 0)
  {
  echo "Error: " . $_FILES["file"]["error"] . "<br />";
  }
else
  {

  }

if ($_FILES["file"]["name"]!="")
{
	if (file_exists("/files/" . $_FILES["file"]["name"]))
    { 
      	echo $_FILES["file"]["name"] . " already exists. ";
    }
    else
    {
	  $fname=$u."-".$time."-".$t."-" . $_FILES["file"]["name"];
	  
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "../../../wp-content/uploads/i-dump-uploads/".$fname);
	  cropImage(60,60,"../../../wp-content/uploads/i-dump-uploads/".$fname,"jpg", "../../../wp-content/uploads/i-dump-uploads/thumbnails/".$fname);
    }
}
}
?>

