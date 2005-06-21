<?PHP

include('common.php');
include('session.php');
global $session;

$sid = $session->getID();
if( !$session->getAuthStatus() ){
	header("Location:login.php?return=diveedit");
	die('');
}

// These arrays hold the DB field names <-> web page field name mappings
$reqd_fields = array("userid"=>"uid",
                     "time_in"=>"start_time",
                     "time_out"=>"end_time",
                     "depth"=>"depth",
                );
$nonreqd_fields = array(
                     "surface_temp"=>"water_temp",
                     "visability"=>"vis",
                     "weight"=>"weight",
                     "windspeed"=>"winds",
                     "waves"=>"waves",
                     "comments"=>"comments",
                     "description"=>"description",
                     "location_city"=>"city",
                     "location_state"=>"state",
                     "location_country"=>"country",
                     "location_coords"=>"coords",
                     "bottom_time"=>"bottom_time",
                     "safety_stop"=>"stop_depth"
                );

if( isset($_GET['action']) && strtoupper($_GET['action']) == 'UPDATE' ){

}
else if( isset($_GET['action']) && strtoupper($_GET['action']) == 'DELETE' ){

}
else if( isset($_GET['action']) && strtoupper($_GET['action']) == 'EDIT' ){
	$divedata = $db->queryRow("SELECT * FROM dives WHERE diveid = ". addslashes($_GET['diveid']) );
	$stpl = new Smarty_divelog();

	foreach( $reqd_fields as $col=>$val ){
		$stpl->assign($val,$divedata[$col]);
	}
	foreach( $nonreqd_fields as $col=>$val ){
		$stpl->assign($val,$divedata[$col]);
	}
	$marker = strpos($divedata['location_coords'],',');
	if( $marker === false ){
		$stpl->assign('coord_lat', '');
		$stpl->assign('coord_lon', '');
	}
	else{
		$stpl->assign('coord_lat', substr($divedata['location_coords'],1,$marker-1) ); //offset 1 to account for open paren in point type
		$stpl->assign('coord_lon', substr($divedata['location_coords'],$marker+1, strlen($divedata['location_coords'])-$marker)-2 );
	}
	$stpl->assign('submit', "Save");
	$stpl->assign('diveid', $divedata['diveid']);
	
	$content = $stpl->fetch('diveedit.html');
	$stpl->assign('login', $session->getAuthStatus()? 'Logout':'Login');
	$stpl->assign('title', "Divelog: Editing dive");
	$stpl->assign('content',$content);
	$stpl->display('shell.html');
	die('');
}
else if( isset($_GET['action']) && strtoupper($_GET['action']) == 'ADD' ){

	$date = $_POST['Date_Year'].'-'.strtoupper(substr($_POST['Date_Month'],0,3)).'-'.$_POST['Date_Day'];
	$_POST['start_time'] = $date .' '.$_POST['start_time'];
	$_POST['end_time'] = $date .' '.$_POST['end_time'];
	
	if( $_POST['coord_lat'] == '' || $_POST['coord_lon'] == ''){
		$_POST['coords'] = "null";
	}
	else{
		$_POST['coords'] = "'(". $_POST['coord_lat'] .",". $_POST['coord_lon'] .")'";
		unset($_POST['coord_lat']);
		unset($_POST['coord_lon']);
	}
	
	$error = array();
	foreach( $reqd_fields as $col=>$val ){
		if( !isset($_POST[$val]) or empty($_POST[$val]) ){
				$error[] = $val;
		}
	}
	if( count($error) > 0 ){
		print "Required fields are not filled in.\n <pre>". print_r($error,true)."</pre>";
		die('');
	}
	if( empty($_POST['bottom_time']) ){
		$_POST['bottom_time'] = "null";
	}
	if( empty($_POST['stop_depth']) ){
		$_POST['stop_depth'] = "null";
	}
	$_POST = unslash_arr($_POST);
	$db->queryResult("SELECT ins_dive(" .
						pg_escape_string($_POST['uid']).",'".
						pg_escape_string($_POST['start_time'])."','".
						pg_escape_string($_POST['end_time'])."',".
						pg_escape_string($_POST['depth']).",".
						pg_escape_string($_POST['water_temp']).",".
						pg_escape_string($_POST['vis']).",".
						pg_escape_string($_POST['weight']).",".
						pg_escape_string($_POST['winds']).",".
						pg_escape_string($_POST['waves']).",'".
						pg_escape_string($_POST['comments'])."','".
						pg_escape_string($_POST['description'])."','".
						pg_escape_string($_POST['city'])."','".
						pg_escape_string($_POST['state'])."','".
						pg_escape_string($_POST['country'])."',".
						pg_escape_string($_POST['coords']).",'".
						pg_escape_string($_POST['bottom_time'])."',".
						pg_escape_string($_POST['stop_depth']).
			")");
	/*
	print "<pre>";
	print_r($sql);
	print "</pre>";
	die('');*/

	$stpl = new Smarty_divelog();
	$stpl->assign('dive_date',$_POST['start_time']);
	$stpl->assign('dive_desc',$_POST['description']);
	
	$content = $stpl->fetch('diveins.html');
	$stpl->assign('title', "Divelog - Added dive.");
	$stpl->assign('login', ($session->getAuthStatus())?"Logout" : "Login" );
	$stpl->assign('content',$content);
	$stpl->display('shell.html');
	die('');
}
else{  //default to a new record

   $stpl = new Smarty_divelog();
   $user = $db->queryRow("SELECT * FROM sel_user_from_sessid('".pg_escape_string($session->getID())."') AS (userid int, username text)");

   $stpl->assign('username', $user['username']);
   $stpl->assign('uid', $user['userid']);
   $stpl->assign('submit', "Add Dive");

   $content = $stpl->fetch('diveedit.html');
   $stpl->assign('title', "divelog entry for ". $user['username']);
   $stpl->assign('login', $session->getAuthStatus()? "Logout" : "Login" );
   $stpl->assign('content',$content);
   $stpl->display('shell.html');
}

?>
