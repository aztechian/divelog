<?PHP

include('common.php');
include('session.php');
global $session;

$sid = $session->getID();

/*if( isset($_GET['action']) && strtoupper($_GET['action']) == 'UPDATE' ){

}
else if( isset($_GET['action']) && strtoupper($_GET['action']) == 'DELETE' ){

}
else */
if( isset($_GET['action']) && strtoupper($_GET['action']) == 'ADD' ){

	$date = $_POST['Date_Year'].'-'.strtoupper(substr($_POST['Date_Month'],0,3)).'-'.$_POST['Date_Day'];
	$_POST['start_time'] = $date .' '.$_POST['start_time'];
	$_POST['end_time'] = $date .' '.$_POST['end_time'];
	
	$_POST['coords'] = "(". $_POST['coord_lat'] .",". $_POST['coord_lon'] .")";
	unset($_POST['coord_lat']);
	unset($_POST['coord_lon']);
	$error = array();
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

	foreach( $reqd_fields as $col=>$val ){
		if( !isset($_POST[$val]) or empty($_POST[$val]) ){
				$error[] = $val;
		}
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
						pg_escape_string($_POST['country'])."','".
						pg_escape_string($_POST['coords'])."','".
						pg_escape_string($_POST['bottom_time'])."',".
						pg_escape_string($_POST['stop_depth']).
			")");
	/*print "<pre>";
	print_r($sql);
	print "</pre>";
	*/
	$user = $db->queryRow("SELECT userid, username FROM sessions, users WHERE session_id='".$session->getID()."' AND session_user_id = userid");
	$stpl = new Smarty_divelog();
	$stpl->assign('dive_date',$_POST['start_time']);
	$stpl->assign('dive_desc',$_POST['description']);
	$content = $stpl->fetch('diveins.html');
	
	$stpl->assign('title', "Divelog - Added dive.");
	$stpl->assign('login', (is_array($user))?"Logout" : "Login" );
	$stpl->assign('content',$content);
	$stpl->display('shell.html');
	die('');
}
else{  //default to a new record

   $stpl = new Smarty_divelog();
   $user = $db->queryRow("SELECT userid, username FROM sessions, users WHERE session_id='".$session->getID()."' AND session_user_id = userid");

   $stpl->assign('username', $user['username']);
   $stpl->assign('uid', $user['userid']);
   
   $content = $stpl->fetch('diveedit.html');
   $stpl->assign('title', "divelog entry for ". $user['username']);
   $stpl->assign('login', (is_array($user))?"Logout" : "Login" );
   $stpl->assign('content',$content);
   $stpl->display('shell.html');
}

?>
