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

	$sql = "INSERT INTO dives VALUES(";
	foreach( $reqd_fields as $col=>$val ){
		$values[] = "'$col'='".addslashes($_POST[$val])."'";
	}
	foreach( $nonreqd_fields as $col=>$val ){
		$values[] = "'$col'='".addslashes($_POST[$val])."'";
	}
	$sql .= implode(",", $values) . ")";

	//$db->queryResult($sql);
	print "<pre>";
	print_r($sql);
	print "</pre>";
	die('');
}
else{  //default to a new record

   $stpl = new Smarty_divelog();
   $user = $db->queryRow("SELECT userid, username FROM sessions, users WHERE session_id='".$session->getID()."' AND session_user_id = userid");

   $stpl->assign('username', $user['username']);
   $stpl->assign('uid', $user['userid']);
   
   $content = $stpl->fetch('diveedit.html');
   $stpl->assign('title', "divelog entry for ". $user['username']);
   $stpl->assign('login',"Logout");
   $stpl->assign('content',$content);
   $stpl->display('shell.html');
}

?>
