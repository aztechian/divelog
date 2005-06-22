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
	//TODO - TASK start_time and end_time could be empty, check them
	$_POST['start_time'] = $date .' '.$_POST['start_time'];
	$_POST['end_time'] = $date .' '.$_POST['end_time'];
	foreach ($nonreqd_fields as $col=>$val) {
		if (!isset($_POST[$val]) or empty($_POST[$val])) {
			$_POST[$val] = 'null';
		}
	}
	
	if( $_POST['coord_lat'] == '' || $_POST['coord_lon'] == ''){
		$_POST['coords'] = 'null';
	}
	else {
		$_POST['coords'] = "'" . $_POST['coord_lat'] .",". $_POST['coord_lon'] .")'";
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
/*
	if( empty($_POST['bottom_time']) ){
		$_POST['bottom_time'] = 'null';
	}
	if( empty($_POST['stop_depth']) ){
		$_POST['stop_depth'] = 'null';
	}
*/
	//should really check every one to make sure they are the corret type
	//best would be some Javascript in the page before submit.
	
	
	$_POST = unslash_arr($_POST);
	//we need to build the query sepparately
	$add_query = 'SELECT ins_dive(' .$_POST['uid'] . ",'" .
					pg_escape_string($_POST['start_time']) . "','" .
					pg_escape_string($_POST['end_time']) . "'," .
					$_POST['depth'] . ',' .
					$_POST['water_temp'] . ',' .
					$_POST['vis'] . ',' .
					$_POST['weight'] . ',' .
					$_POST['winds'] . ',' .
					$_POST['waves'] . ',';
	if ($_POST['comments'] == 'null') {
		$add_query .= 'null,';
	}
	else {	
		$add_query .= pg_escape_string($_POST['comments']) .',';
	}
	if ($_POST['description'] == 'null') {
		$add_query .= 'null,';
	}
	else {	
		$add_query .= pg_escape_string($_POST['description']) .',';
	}
	if ($_POST['city'] == 'null') {
		$add_query .= 'null,';
	}
	else {	
		$add_query .= pg_escape_string($_POST['city']) .',';
	}
	if ($_POST['state'] == 'null') {
		$add_query .= 'null,';
	}
	else {	
		$add_query .= pg_escape_string($_POST['state']) .',';
	}
	if ($_POST['country'] == 'null') {
		$add_query .= 'null,';
	}
	else {	
		$add_query .= pg_escape_string($_POST['country']) .',';
	}
	$add_query .= $_POST['coords'] . ','; //set right earlier
	if ($_POST['bottom_time'] == 'null') {
		$add_query .= 'null,';
	}
	else {	
		$add_query .= pg_escape_string($_POST['bottom_time']) .',';
	}
	$add_query .= $_POST['stop_depth'] . ')';				
/*
	if ($db->queryResult('SELECT ins_dive(' .
						$_POST['uid'] .',' .
						pg_escape_string($_POST['start_time']) .','.
						pg_escape_string($_POST['end_time']) .','.
						$_POST['depth'] .','.
						pg_escape_string($_POST['water_temp']) .','.
						pg_escape_string($_POST['vis']) .','.
						pg_escape_string($_POST['weight']) .','.
						pg_escape_string($_POST['winds']) .','.
						pg_escape_string($_POST['waves']) .','.
						pg_escape_string($_POST['comments']) .','.
						pg_escape_string($_POST['description']) .','.
						pg_escape_string($_POST['city']) .','.
						pg_escape_string($_POST['state']) .','.
						pg_escape_string($_POST['country']) .','.
						pg_escape_string($_POST['coords']) .','.
						pg_escape_string($_POST['bottom_time']) .','.
						pg_escape_string($_POST['stop_depth']) .
			')'))
*/
	$db->queryResult($add_query);
	$add_attempt_message = $db->error;
	if (empty($add_attempt_message)) {
		$add_attempt_message = 'Dive successfuly added on';
		//build the text here
	}	
		
	/*
	print "<pre>";
	print_r($sql);
	print "</pre>";
	die('');*/
	//select ins_dive(1,'2005-06-21 ','2005-06-21',8,null,null,null,null,null,null,null,null,null,null,null,null,null);

	$stpl = new Smarty_divelog();
	//first fetch then assign, otherwise stuff gets lost sometimes
	//backwards .... I am confused now ... sometimes it needs to be backwards
	
	$stpl->assign('dive_add_message', $add_attempt_message);
	$stpl->assign('dive_date',$_POST['start_time']);
	$stpl->assign('dive_desc',$_POST['description']);

	$content = $stpl->fetch('diveins.html');	

	$stpl->assign('title', "Divelog - Add dive");
	$stpl->assign('login', ($session->getAuthStatus())?"Logout" : "Login" );
	$stpl->assign('content',$content);
	$stpl->display('shell.html');

	die('');
}
else{  //default to a new record

   $stpl = new Smarty_divelog();
   $user = $db->queryRow("SELECT * FROM sel_user_from_sessid('".pg_escape_string($session->getID())."') AS (userid int, username text)");
   $content = $stpl->fetch('diveedit.html');
   $stpl->assign('username', $user['username']);
   $stpl->assign('uid', $user['userid']);
   $stpl->assign('submit', "Add Dive");

   $stpl->assign('title', "divelog entry for ". $user['username']);
   $stpl->assign('login', $session->getAuthStatus()? "Logout" : "Login" );
   $stpl->assign('content',$content);
   $stpl->display('shell.html');
}

?>
