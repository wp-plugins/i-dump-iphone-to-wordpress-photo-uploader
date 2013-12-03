<?php
/**
 * Plugin Name: i-Dump Windows Destop and iPhone Uploader
 * Plugin URI: http://i-dump.info
 * Description: Upload images Directly from your desktop or iPhone WP-Dump into your wordpress i-Dump gallery.  
 * Version: 1.8
 * Author: Daan van der Werf 
 * Author URI: http://daanvanderwerf.nl
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
// create custom plugin settings menu
add_action('admin_menu', 'idump_create_menu');
add_option("jal_db_version", "1.0");
register_activation_hook(__FILE__,'jal_install');


global $jal_db_version;
$jal_db_version = "1.0";

function jal_install () {
   global $wpdb;
   global $jal_db_version;

   $table_name = $wpdb->prefix . "iphoto";
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {  
      $sql = "CREATE TABLE IF NOT EXISTS `iphoto` (
   `id` bigint(20) NOT NULL AUTO_INCREMENT,
   `file` text NOT NULL,
   `username` text NOT NULL,
   `date` text NOT NULL,
   `geo` text NOT NULL,
   `text` text NOT NULL,
   `gallery` text NOT NULL,
   `publish` int NOT NULL,
  PRIMARY KEY (`id`)
	);";
	
	
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
 
 $wpdb->insert( $table_name, array( 'date' => current_time('mysql'), 'username' => 'i-dump', 'file' => 'daan-1285674781--keyboard.jpg' ) );

   add_option("jal_db_version", $jal_db_version);

   }
}


function idump_create_menu() {

	//create new top-level menu
	add_menu_page('i-Dump Plugin Settings', 'i-Dump Gallery', 'administrator', __FILE__, 'idump_settings_page',plugins_url('/images/idump.png', __FILE__));
	//call register settings function
	add_action( 'admin_init', 'register_idumpsettings' );
}


function register_idumpsettings() {
	//register our settings
	register_setting( 'idump-settings-group', 'idump_username' );
	register_setting( 'idump-settings-group', 'idump_password' );
	register_setting( 'idump-settings-group', 'idump_username1' );
	register_setting( 'idump-settings-group', 'idump_password1' );
	register_setting( 'idump-settings-group', 'idump_folder' );
	register_setting( 'idump-settings-group', 'idump_glimit' );
	register_setting( 'idump-settings-group', 'idump_gcols' );
	register_setting( 'idump-settings-group', 'idump_gtext' );
	register_setting( 'idump-settings-group', 'idump_support');
	register_setting( 'idump-settings-group', 'idump_premod');
	register_setting( 'idump-settings-group', 'idump_upgrade');
}




///// Start shortcode
function showdumps(){
      $blogname = get_bloginfo('name');
      if ((get_option('idump_support') != '')|| (get_option('idump_support') =='on')) {
        $add = "<font style='font-size:x-small'> ".$blogname ." is powered by <a href='http://www.i-dump.info/' target='_blank' style='text-decoration:none'>i-Dump</a>. </font>";
       }
    
   //Start photo viewer
  $query = "SELECT * FROM `iphoto`";
  $sql = mysql_query($query) or die ( mysql_error( ) ); 
  while ($record = mysql_fetch_object($sql))
  

$filesql=$record->file; 

$blog_url = get_bloginfo('wpurl');

$path = $blog_url . "/wp-content/uploads/i-dump-uploads";
$path1 = $blog_url . "/wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader";
$path2 = $blog_url . "/wp-content/uploads/i-dump-uploads/thumbnails";
$glimit = get_option('idump_glimit');
$gcols = get_option('idump_gcols');
$gtext = get_option('idump_gtext');

if ($glimit == '' || $gcols == ''){
   $gtext ='<img src="'.$blog_url.'/wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/images/sign_warning.png"><b><font color=red> Please set your limits and photos per row at your i-Dump management</font></b>';
   $glimit = 0;
   $gcols = 0;
}
$gcols = $gcols +1;

$i=1;
echo '
';

echo $gtext;// the text above the gallery method
echo"<span class='caption'><center><table><tr>";

 $query = "SELECT * FROM `iphoto` WHERE publish = 1 ORDER BY `id` DESC LIMIT $glimit";
  $sql = mysql_query($query) or die ( mysql_error( ) ); 
  while ($record = mysql_fetch_object($sql)){


$filesql=$record->file; 

if ($record->text == ''||$record->text == '(null)'){
$mark= date('Y-m-d', $record->date);
$markfull = date('Y-m-d', $record->date);
}else{
$mark= $record->text ;
$markfull = $record->text;
}

$limit = 14;
   if (strlen($mark) > $limit)
      $mark = substr($mark, 0, strrpos(substr($mark, 0, $limit), ' ')) . '...';

    echo "<td width=90><center>
	<a href='$path/$filesql' class='screenshot' rel='$path/$filesql' title=\"$markfull\"><img src=\"$path2/$filesql\" alt=\"$markfull\" /></a>
	<br><font style='font-size:x-small;'>$mark</font></center></td>";
	
//	echo "<td width=90><center>
//	<a title='$markfull' class='screenshot' rel='$path/$filesql' height='250px'><img alt='$markfull' src='$path2/$filesql' class='preview'></a>
//	<br><font style='font-size:x-small;'>$mark</font></center></td>";
	
	
	

        $i++;
		if ($i== $gcols)
		
		{
		echo"</tr><tr>";
		$i= 1;
		
		}

}
echo"</tr></table>".$add."</center></span>";
}

add_shortcode('idumpgallery', 'showdumps');
// end shortcode


function idump_settings_page() {
?>


<script type="text/javascript" language="javascript">
function check_all(){ 
    var node_list = document.getElementsByTagName('input'); 
    for (var i = 0; i < node_list.length; i++) { 
        var node = node_list[i]; 
        if (node.getAttribute('type') == 'checkbox') { 
			if (node.checked == 1){
            node.checked = 0; }
			else {
			node.checked = 1; }
        } 
    } 
}
</script>





<div class="wrap">
  
<?php
function getDirectorySize($path)
{
  $totalsize = 0;
  $totalcount = 0;
  $dircount = 0;
  if ($handle = opendir ($path))
  {
    while (false !== ($file = readdir($handle)))
    {
      $nextpath = $path . '/' . $file;
      if ($file != '.' && $file != '..' && !is_link ($nextpath))
      {
        if (is_dir ($nextpath))
        {
          $dircount++;
          $result = getDirectorySize($nextpath);
          $totalsize += $result['size'];
          $totalcount += $result['count'];
          $dircount += $result['dircount'];
        }
        elseif (is_file ($nextpath))
        {
          $totalsize += filesize ($nextpath);
          $totalcount++;
        }
      }
    }
  }
  closedir ($handle);
  $total['size'] = $totalsize;
  $total['count'] = $totalcount;
  $total['dircount'] = $dircount;
  return $total;
}

function sizeFormat($size)
{
    if($size<1024)
    {
        return $size." bytes";
    }
    else if($size<(1024*1024))
    {
        $size=round($size/1024,1);
        return $size." KB";
    }
    else if($size<(1024*1024*1024))
    {
        $size=round($size/(1024*1024),1);
        return $size." MB";
    }
    else
    {
        $size=round($size/(1024*1024*1024),1);
        return $size." GB";
    }

}  
?>
<? 
if (!is_dir('../wp-content/uploads/i-dump-uploads')) {
	$oldumask = umask(0);
    mkdir('../wp-content/uploads/i-dump-uploads', 0755);
	umask($oldumask);
}

if (!is_dir('../wp-content/uploads/i-dump-uploads/thumbnails')) {
	$oldumask = umask(0);
    mkdir('../wp-content/uploads/i-dump-uploads/thumbnails', 0755);
	umask($oldumask);
}

//////// TO REMOVE COMPLETE UPLOAD FOLDERS ////////////////
//rmdir('../wp-content/uploads/i-dump-uploads/thumbnails');
//rmdir('../wp-content/uploads/i-dump-uploads');
//////// END REMOVE COMPLETE UPLOAD FOLDERS ///////////////


/////////add colum to publish//  upgrade to version 1.5 or higher

$version="1.7"; 

if ((get_option('idump_upgrade') == '0')||(get_option('idump_upgrade') == '')) { 
	$sql=mysql_query("SELECT publish FROM iphoto");
				if (!$sql){
					mysql_query("ALTER TABLE iphoto ADD publish int NOT NULL AFTER gallery");
					echo 'updated to version '.$version.'';
						}
	update_option( 'idump_upgrade', '1' );
}



/////end add column 'publish'//

?>

<div id="gallery" class="postbox">
<h2 style="padding-left:20px">I-Dump uploaded Images </h2>

<a href="http://www.i-dump.info"><img src="../wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/images/i-dump-logo-backoffice.jpg" height="122" width="122" alt="i-Dump iPhone to Wordpress app " width="175" height="111" border="0" align="right" /></a>

        <?php
        $querycount = "SELECT id FROM `iphoto`";
        $sqlcount = mysql_query($querycount) or die ( mysql_error( ) );
        $count = mysql_num_rows($sqlcount);
		    $path="../wp-content/uploads/i-dump-uploads/";
	$ar=getDirectorySize($path);
	echo "<div> <small>&nbsp;&nbsp;You have $count public dumps in your database || Total Gallery Directory Size : ".sizeFormat($ar['size'])."</small></div><br>";
		?>
		
  
<form method="post" name="showpics">
<?php 


$blog_url = get_bloginfo('wpurl');

$path = $blog_url . "/wp-content/uploads/i-dump-uploads/";
$path1 = $blog_url . "/wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader";
$path2 = $blog_url . "/wp-content/uploads/i-dump-uploads/thumbnails";


/////// posttracker
//	print("<pre>");
//	print_r($_POST);
//	print("</pre>");
///////end posttracker


if($_POST['delete']) 
	 {
	 if(($_POST['checkbox'])=='') {echo '<div class="updated" id="message"><p><strong>No images selected...</strong>.</p></div>';} else
		{
	 	echo  '<div class="updated" id="message"><FONT COLOR="RED"><b>number ';
 		$checkbox = $_POST['checkbox'];
		$countCheck = count($_POST['checkbox']);
		for($i=0;$i<$countCheck;$i++)
		{
			$del_id  = $checkbox[$i];
 			$sql = "DELETE from iphoto where id = $del_id";
			$del_sql = mysql_query($sql) or die ( mysql_error( ) );
			echo  $del_id.' , ';
			
		 }
		echo  'removed from gallery.</b></div></FONT>';	
	 }
	 }
	 
if($_POST['publish']) 
	 {
	 	if(($_POST['checkbox'])=='') {echo '<div class="updated" id="message"><p><strong>No images selected...</strong>.</p></div>';} else
		{
		
	 	echo  '<div class="updated" id="message"><FONT COLOR="GREEN"><b>number ';
 		$checkbox = $_POST['checkbox'];
		$countCheck = count($_POST['checkbox']);
		for($i=0;$i<$countCheck;$i++)
		{
			$pub_id  = $checkbox[$i];
 			$sql = "UPDATE iphoto SET publish= 1 WHERE id = $pub_id";
			$pub_sql = mysql_query($sql) or die ( mysql_error( ) );
			echo  $pub_id.',';
		 }
		echo  'published.</b></font></div>';	
	 }	 
	 }
	 
if($_POST['unpublish']) 
	 {
	 if(($_POST['checkbox'])=='') {echo '<div class="updated" id="message"><p><strong>No images selected...</strong>.</p></div>';} else
		{
		
	 	echo  '<div class="updated" id="message"><FONT COLOR="GREEN"><b>number ';
 		$checkbox = $_POST['checkbox'];
		$countCheck = count($_POST['checkbox']);
		for($i=0;$i<$countCheck;$i++)
		{
			$unpub_id  = $checkbox[$i];
 			$sql = "UPDATE iphoto SET publish= 0 WHERE id = $unpub_id";
			$unpub_sql = mysql_query($sql) or die ( mysql_error( ) );
			echo  $unpub_id.',';
		 }
		echo  'unpublished.</b></font></div>';	
	 }	
	 }
	


	 

// $myFile = $path.$record->file;
// unlink($myFile);

//////end delete

//////////////////////
$sql = "SELECT * FROM iphoto ORDER BY id DESC ";
$ans= mysql_query($sql) or die(mysql_error());
$results = array();

while($row = @mysql_fetch_assoc($ans)){
	$results[]=$row;
$j=1;	
}

echo"&nbsp <table><tr>";
$page = (isset($_POST['page'])) ? intval($_POST['page']) : 1;
$begin_iteration = ($page-1)*55;
$end_iteration = $begin_iteration+55;

for($i = $begin_iteration;$i<$end_iteration;$i++){
	if(@is_array($results[$i])){
	
	if ($results[$i]['publish'] == 1){
	$show ='<img src="../wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/images/ok.png">';
	}else {
	$show ='<img src="../wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/images/not.png">';
	}
	
	
	  $time= date('Y-m-d', $results[$i]['date']);
	    echo "<td><label for='checkbox[".$i."]'><small>$time</small><br><img src=\"$path2/".$results[$i]['file']."\" alt=\"$time\" /><br><center>

		<small>".$results[$i]['id']." </small><input name=\"checkbox[]\" type=\"checkbox\" id=\"checkbox[".$i."]\" value=".$results[$i]['id'].">
				
		
		" .$show." </center></label>
</td>";

	
		        $j++;
		if ($j== 12)
		
		{
		echo"</tr><tr>";
		$j= 1;
		
		}
		
	}else{
		break;
		
	}
}

$pages = ceil(sizeof($results) / 55);
//Number of pages

for($i = 1;$i<=$pages;$i++)

echo "<button name=page type=submit value=$i>Page $i</button>";
echo"</tr></table></style><br>
<input type=\"button\" name=\"checkall\" onclick=\"check_all()\" value=\"Select/Deselect All\" style=\"width: 150px\" >  <input type=\"submit\" value=\"Publish Selected\" name=\"publish\" id=\"publish\" style=\"width: 150px\"> <input type=\"submit\" value=\"Unpublish Selected\" name=\"unpublish\" id=\"unpublish\" style=\"width: 150px\"> <input type=\"submit\" value=\"Delete selected\" name=\"delete\" id=\"delete\" style=\"width: 150px\">";

/////////////////////

echo "</form><br>";

echo " &nbsp <small>Images will <b>not</b> be destroyed but available in the files folder of your WordPress Upload folder. Thank you for using <a href='http://www.i-dump.info/'>i-Dump</a>.</small>";

?>

</div>

<div id="users" class="postbox">
<h2 style="padding-left:20px">i-Dump Upload Management Settings</h2>
<strong>&nbsp Use the exact settings on your iPhone to allow uploading mobile photos</strong>


<form method="post" action="options.php">
    <?php settings_fields( 'idump-settings-group' ); ?>
    <?php
    if ((get_option('idump_password')=='')&&(get_option('idump_username')=='')||(get_option('idump_password1')=='')&&(get_option('idump_username1')=='')) {
    echo '&nbsp <b><font color=red>Your profiles are not using a password.Are you sure you allow public uploads?</font></b><br>';
    }
    if ((get_option('idump_glimit')=='')||(get_option('idump_gcols')=='')) {
    echo '&nbsp <b><font color=red>Your frontside gallery will not show up, please do not leave the limit & rows empty in your gallery settings</font></b><br>';
    }
    
    ?>
    
    
     &nbsp By default you can send test images from your iPhone to <a h href="http://www.i-dump.info" target="_blank">www.i-dump.info</a><br />
   <?php
   
$blog_url = get_bloginfo('wpurl');
$blog_urlrepl = Str_replace('http://www.','',$blog_url);
$folder = '../wp-content/uploads/i-dump-uploads';

if(stristr($blog_url, 'http://www') === FALSE) {
    $blog_urlrepl = Str_replace('http://','',$blog_url);
  } 
if (is_writable($folder)) {
    echo ' &nbsp Your domain url for the i-Dump for Windows Application or WP-Dump iPhone app : <b>',$blog_urlrepl,'</b>';// folder is correct
} else {
    echo ' &nbsp <img src="../wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/images/sign_warning.png"> Folders uploads/i-dump-uploads/<b> & /thumbnails</b> need cmod 777, please adjust it for personal usage.<img src="../wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/images/sign_warning.png">';
}
?>
<br><br>

    <table class="form-table">
        <tr valign="top">
        <th scope="row">1 st Username</th>
        <td><input type="text" name="idump_username" value="<?php echo get_option('idump_username'); ?>" />          
          (testmode: public)</td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Password</th>
        <td><input type="text" name="idump_password" value="<?php echo get_option('idump_password'); ?>" />
        (testmode: pass)</td>
        </tr>
		        <tr valign="top">
        <th scope="row">2 nd Username</th>
        <td><input type="text" name="idump_username1" value="<?php echo get_option('idump_username1'); ?>" />          
          </td>
        </tr>
	 <tr valign="top">
        <th scope="row">Password</th>
        <td><input type="text" name="idump_password1" value="<?php echo get_option('idump_password1'); ?>" />
        </td>
        </tr>
	 </table>
	 <p class="submit">
	 <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	 </p>	 
	 </div>

	 
       <div id="settings" class="postbox">
	    
    <?php
   if ((get_option('idump_support') != '')|| (get_option('idump_support') =='on')) {
     $support = 'checked';
   $thankyou = '<font color="green">Thank you for supporting us !</font>';
   } else {  
    $support = 'unchecked';
    $thankyou = 'Show some love :-)';
  }
  ////premoderated
     if ((get_option('idump_premod') != '')|| (get_option('idump_premod') =='on')) {
     $moderated = 'checked';
   $premod = '<font color="red">You are moderating your uploads.</font>';
   } else {  
    $moderated = 'unchecked';
    $premod = 'Use this option if you moderate uploads before publishing';
  }
  /////end premoderated
   ?>
      
      <h2 style="padding-left:20px">Modify your gallery view settings</h2>
	<table class="form-table">
	<tr valign="top">
        <th scope="row">Text above gallery</th>
        <td>
	 <TEXTAREA COLS="50" rows="5" name="idump_gtext"><?php echo get_option('idump_gtext'); ?></TEXTAREA>
        </td>
        </tr> 
	<tr valign="top">
        <th scope="row">Max dumps in gallery</th>
        <td><input type="text" name="idump_glimit" value="<?php echo get_option('idump_glimit'); ?>" />
        </td>
        </tr>
	<tr valign="top">
        <th scope="row">Max dumps per row</th>
        <td><input type="text" name="idump_gcols" value="<?php echo get_option('idump_gcols'); ?>" />
        </td>
        </tr>
		 <tr valign="top">
        <th scope="row">Pre-moderate uploads</th>
	
        <td><input type="checkbox" name="idump_premod" <? echo $moderated ?>>  <?PHP print $premod; ?>
        </td>
        </tr>
	 <tr valign="top">
        <th scope="row">'Powered by i-Dump'</th>
	
        <td><input type="checkbox" name="idump_support" <? echo $support ?>>  <?PHP print $thankyou; ?>
        </td>
        </tr>
	 
	    </table>
    
	
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
    </p>


</div>
</form>



<div id="sc4" class="tabcontent">

   <h2 style="padding-left:20px"> A few tips to follow </h2><br>
 <ol>  
<li> In wordpress you could use the<b> [idumpgallery]</b> tag anywhere post or blog to show your latest Dumps at your blog.</li>
<li> When there the username & password are not specified, people are alowed to use WP-Dump without any username & password, please bewared of that!</li>
<li> Make sure your folderpermissions are set correctly. If the folderpermissions are not correct you probably will see only timestamps while uploading.</li>
<li> Make sure your server have no restrictions to write into the uploads/i-dump-uploads/ folder, else you wont see your pics but only dates! </li>
<li> Your mobile uploads will never be deleted but will always be available at your <i>uploads/i-dump-uploads/</i> folder. 
<li> Make sure your domain you entered at the WP-Dump iPhone app does not contain 'http://www' but enter it like <b> <? echo $blog_urlrepl ?> </b></li>
<li> This plugin works great with the <a href="http://wordpress.org/extend/plugins/fancybox-for-wordpress/">Fancybox for WordPress plugin</a>. Next versions will be prepared for others.
<li> If your having troubles with installing it you can contact us at dev@i-dump.info or jump to our site <a href='http://www.i-dump.info/' target="_blank">i-Dump.info</a> and fill in the contact form.</li>
<li><b> If you wish to let iPhone users dump at your blog we advise you to promote this awsome free plugin !</b></li>
</ol>
<br><br>

<center>
<table>
   <tr>
      <td>
	 If you realy like what you see then<br> please help to keep this project alive!
      </td>
      <td>
      <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
      <input type="hidden" name="cmd" value="_s-xclick">
      <input type="hidden" name="hosted_button_id" value="6ZP2QDY6WN75Y">
      <input type="image" src="https://www.paypal.com/en_US/NL/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
      <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
      </form>
   </td>
   </tr>
</table>
</center>
</div>

</div>



<?
} 
?>