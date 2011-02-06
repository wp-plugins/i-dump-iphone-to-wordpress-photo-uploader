<?php
/**
 * Plugin Name: i-Dump iPhone Photo Uploader
 * Plugin URI: http://i-dump.info
 * Description: Upload photos or images from your iPhone WP-Dump app directly into your wordpress i-Dump gallery.  
 * Version: 1.2
 * Author: Daan van der Werf 
 * Author URI: http://2bdaan.com
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
	add_menu_page('i-Dump Plugin Settings', 'i-Dump Settings', 'administrator', __FILE__, 'idump_settings_page',plugins_url('/images/idump.png', __FILE__));
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
echo $gtext;// the text above the gallery method
echo"<span class='caption'><center><table><tr>";

 $query = "SELECT * FROM `iphoto` ORDER BY `id` DESC LIMIT $glimit";
  $sql = mysql_query($query) or die ( mysql_error( ) ); 
  while ($record = mysql_fetch_object($sql)){

$time= date('Y-m-d', $record->date);
$filesql=$record->file; 


    echo "<td><center><a href='$path/$filesql' class='lightview' rel='gallery[mygallery]' ><img src=\"$path2/$filesql\" alt=\"$time\" /></a><br><font style='font-size:x-small;'>$time</font></center></td>";

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

<style type="text/css">
#tablist{
padding: 3px 0;
margin-left: 0;
margin-bottom: 5px;
margin-top: 0.1em;
font: bold 11px Verdana;
}
#tablist li{
list-style: none;
display: inline;
margin: 0;
}
#tablist li a{
margin-left: 3px;
border: 1px solid #778;
border-bottom: none;
background: white;
float:left;
overflow:hidden;
padding:3px 10px 0px 10px;
}
#tablist li a img{
float:left;
overflow:hidden;
margin:0 5px 0 2px;
}
#tablist li a:link, #tablist li a:visited{
color: black;
text-decoration: none;
}
#tablist li a.current{
background: #D8D8D8;
text-decoration: none;
}
#tabcontentcontainer{
width: 1000px;
/* Insert Optional Height definition here to give all the content a unified height */
padding: 5px;
border: 1px solid black;
margin:25px 0 0 0;
}
.tabcontent{
display:none;
}

.cross{
 background: url(../wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/images/delete.png) no-repeat scroll left top transparent;
 border: 0 !important;
 background-color: #D8D8D8 !important;
 cursor:pointer;
}
</style>


<script type="text/javascript">

/***********************************************
* Tab Content script- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

//Set tab to intially be selected when page loads:
//[which tab (1=first tab), ID of tab content to display]:
var initialtab=[3, "sc3"]

////////Stop editting/////////////

function cascadedstyle(el, cssproperty, csspropertyNS){
if (el.currentStyle)
return el.currentStyle[cssproperty]
else if (window.getComputedStyle){
var elstyle=window.getComputedStyle(el, "")
return elstyle.getPropertyValue(csspropertyNS)
}
}

var previoustab=""

function expandcontent(cid, aobject){
if (document.getElementById){
highlighttab(aobject)
detectSourceindex(aobject)
if (previoustab!="")
document.getElementById(previoustab).style.display="none"
document.getElementById(cid).style.display="block"
previoustab=cid
if (aobject.blur)
aobject.blur()
return false
}
else
return true
}

function highlighttab(aobject){
if (typeof tabobjlinks=="undefined")
collecttablinks()
for (i=0; i<tabobjlinks.length; i++)
tabobjlinks[i].style.backgroundColor=initTabcolor
var themecolor=aobject.getAttribute("theme")? aobject.getAttribute("theme") : initTabpostcolor
aobject.style.backgroundColor=document.getElementById("tabcontentcontainer").style.backgroundColor=themecolor
}

function collecttablinks(){
var tabobj=document.getElementById("tablist")
tabobjlinks=tabobj.getElementsByTagName("A")
}

function detectSourceindex(aobject){
for (i=0; i<tabobjlinks.length; i++){
if (aobject==tabobjlinks[i]){
tabsourceindex=i //source index of tab bar relative to other tabs
break
}
}
}

function do_onload(){
var cookiename=(typeof persisttype!="undefined" && persisttype=="sitewide")? "tabcontent" : window.location.pathname
var cookiecheck=window.get_cookie && get_cookie(cookiename).indexOf("|")!=-1
collecttablinks()
initTabcolor=cascadedstyle(tabobjlinks[1], "backgroundColor", "background-color")
initTabpostcolor=cascadedstyle(tabobjlinks[0], "backgroundColor", "background-color")
if (typeof enablepersistence!="undefined" && enablepersistence && cookiecheck){
var cookieparse=get_cookie(cookiename).split("|")
var whichtab=cookieparse[0]
var tabcontentid=cookieparse[1]
expandcontent(tabcontentid, tabobjlinks[whichtab])
}
else
expandcontent(initialtab[1], tabobjlinks[initialtab[0]-1])
}

if (window.addEventListener)
window.addEventListener("load", do_onload, false)
else if (window.attachEvent)
window.attachEvent("onload", do_onload)
else if (document.getElementById)
window.onload=do_onload


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


?>

<h2>i-Dump Upload Management Settings<span class="style2"><a href="http://itunes.apple.com/us/app/wp-dump/id413231620?mt=8&ls=1"><img src="../wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/images/i-dump-logo-backoffice.jpg" height="122" width="122" alt="i-Dump iPhone to Wordpress app " width="175" height="111" border="0" align="right" /></a></span></h2>
<p><strong>Use the exact settings on your iPhone to allow uploading mobile photos</strong></p>

<form method="post" action="options.php">
    <?php settings_fields( 'idump-settings-group' ); ?>
    <?php
    if ((get_option('idump_password')=='')&&(get_option('idump_username')=='')||(get_option('idump_password1')=='')&&(get_option('idump_username1')=='')) {
    echo '<b><font color=red>Your profiles are not using a password.Are you sure you allow public uploads?</font></b><br>';
    }
    if ((get_option('idump_glimit')=='')||(get_option('idump_gcols')=='')) {
    echo '<b><font color=red>Your frontside gallery will not show up, please do not leave the limit & rows empty in your gallery settings</font></b><br>';
    }
    
    ?>
    
    
    By default you can send test images from your iPhone to <a h href="http://www.i-dump.info" target="_blank">www.i-dump.info</a><br />
   <?php
   
$blog_url = get_bloginfo('wpurl');
$blog_urlrepl = Str_replace('http://www.','',$blog_url);
$folder = '../wp-content/uploads/i-dump-uploads';

if(stristr($blog_url, 'http://www') === FALSE) {
    $blog_urlrepl = Str_replace('http://','',$blog_url);
  } 
if (is_writable($folder)) {
    echo 'Your domain url of the WP-Dump iPhone app : <b>',$blog_urlrepl,'</b>';// folder is correct
} else {
    echo '<img src="../wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/images/sign_warning.png"> Folders uploads/i-dump-uploads/<b> & /thumbnails</b> need cmod 777, please adjust it for personal usage.<img src="../wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/images/sign_warning.png">';
}
?>
<br><br>
<ul id="tablist">
<li><a href="#" onClick="return expandcontent('sc1', this)" theme="#D8D8D8"><img src="../wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/images/group_key.png"> Permission Settings</a></li>
<li><a href="#" onClick="return expandcontent('sc2', this)" theme="#D8D8D8"><img src="../wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/images/cog.png"> Gallery Settings</a></li>
<li><a href="#" onClick="return expandcontent('sc3', this)" theme="#D8D8D8"><img src="../wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/images/picture_go.png"> Gallery Uploads</a></li>
<li><a href="#" onClick="return expandcontent('sc4', this)" theme="#D8D8D8"><img src="../wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/images/lightbulb.png"> Help & Support</a></li>
</ul>

<DIV id="tabcontentcontainer">

<div id="sc1" class="tabcontent">
<h2>Add users or profiles to allow uploading </h2>
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
         <div id="sc2" class="tabcontent">
	    <table>
    
    <?php
   if ((get_option('idump_support') != '')|| (get_option('idump_support') =='on')) {
     $support = 'checked';
   $thankyou = '<font color="green">Thank you for supporting us !</font>';
   } else {  
    $support = 'unchecked';
    $thankyou = '';
  }
   ?>
      
      <h2>Modify your gallery view settings</h2>
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
        <th scope="row">'Powered by i-Dump'</th>
	
        <td><input type="checkbox" name="idump_support" <? echo $support ?>>  <?PHP print $thankyou; ?>
        </td>
        </tr>
	 
	    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
    </p>

</form>
</div>


<div id="sc3" class="tabcontent">
<h2>Manage your uploaded images</h2>


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


if (isset($_POST['next'])) { // next 10 

}else {

}


if (isset($_POST['delete']))
#
{
$delete = $_POST['delete'];
$imageid = $_POST['imageid'];

 echo  '<center<FONT COLOR="RED"><b>..image ',$delete,' removed from gallery.</b></center></FONT>';
 
  $query = "DELETE FROM `iphoto` WHERE`id` LIKE '$delete'";
  $sql = mysql_query($query) or die ( mysql_error( ) ); 
// $myFile = $path.$record->file;
// unlink($myFile);

}

//////////////////////
$sql = "SELECT * FROM iphoto ORDER BY id DESC ";
$ans= mysql_query($sql) or die(mysql_error());
$results = array();

while($row = @mysql_fetch_assoc($ans)){
	$results[]=$row;
$j=1;	
}
echo"<center><table><tr>";
$page = (isset($_POST['page'])) ? intval($_POST['page']) : 1;
$begin_iteration = ($page-1)*55;
$end_iteration = $begin_iteration+55;

for($i = $begin_iteration;$i<$end_iteration;$i++){
	if(@is_array($results[$i])){
	  $time= date('Y-m-d', $results[$i]['date']);
	    echo "<td><img src=\"$path2/".$results[$i]['file']."\" alt=\"$time\" /><input type=\"image\" src=\"$blog_url/wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/images/delete.png\" value=".$results[$i]['id']." name=\"delete\">
<br><small>$time</small></td>";

	
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
echo"</tr></table></style></center><br>";

/////////////////////

echo "</form><br>";

echo "<center><small>Images will <b>not</b> be destroyed but available in the files folder of your WordPress Upload folder. Thank you for using <a href='http://www.i-dump.info/'>i-Dump</a>. </center></small>";



?>
</div>
<div id="sc4" class="tabcontent">

   <h2> A few tips to follow </h2><br>
 <ol>  
<li> In wordpress you could use the<b> [idumpgallery]</b> tag anywhere post or blog to show your latest Dumps at your blog.</li>
<li> When there the username & password are not specified, people are alowed to use WP-Dump without any username & password, please bewared of that!</li>
<li> Make sure your folderpermissions are set correctly. If the folderpermissions are not correct you probably will see only timestamps while uploading.</li>
<li> Make sure your server have no restrictions to write into the uploads/i-dump-uploads/ folder, else you wont see your pics but only dates! </li>
<li> Your mobile uploads will never be deleted but will always be available at your <i>uploads/i-dump-uploads/</i> folder. 
<li> Make sure your domain you entered at the WP-Dump iPhone app does not contain 'http://www' but enter it like <b> <? echo $blog_urlrepl ?> </b></li>
<li> If your having troubles with installing it you can contact us at dev@i-dump.info or jump to our site <a href='http://www.i-dump.info/'>i-Dump.info</a> and fill in the contact form.</li>
<li> If you wish to let iPhone users dump at your blog we advise you to promote this awsome free plugin !</li>
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
<?
} 
?>