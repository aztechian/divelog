<?PHP

include('common.php');
include('session.php');
global $session;

$sid = $session->getID();
if( !$session->getAuthStatus() ){
	header("Location:login.php?return=diveedit");
	die('');
}

$field_types = array(
		     "userid"=>"integer",
                     "time_in"=>"timestamp",
                     "time_out"=>"timestamp",
                     "depth"=>"integer",
                     "surface_temp"=>"integer",
                     "visability"=>"integer",
                     "weight"=>"integer",
                     "windspeed"=>"integer",
                     "waves"=>"integer",
                     "comments"=>"text",
                     "description"=>"text",
                     "location_city"=>"text",
                     "location_state"=>"text",
                     "location_country"=>"text",
                     "location_coords"=>"point",
                     "bottom_time"=>"interval",
                     "safety_stop"=>"interval"
		);

// These arrays hold the DB field names <-> web page field name mappings
// ============================ NOTICE =================================
//  Do NOT change the order of these items in the array, they are matched to
//  the fields as they must go into the ins_dive Database function. If changed,
//  you will break the functionality of adding new dives.
// ======================== END OF NOITCE===============================
//
// all_fields hold an array of every field that is needed to insert a dive record.
// the values are initially blank so that they can be filled in later with the data
// to be inserted.
$all_fields = array(
		     "userid"=>"",
                     "time_in"=>"",
                     "time_out"=>"",
                     "depth"=>"",
                     "surface_temp"=>"",
                     "visability"=>"",
                     "weight"=>"",
                     "windspeed"=>"",
                     "waves"=>"",
                     "comments"=>"",
                     "description"=>"",
                     "location_city"=>"",
                     "location_state"=>"",
                     "location_country"=>"",
                     "location_coords"=>"",
                     "bottom_time"=>"",
                     "safety_stop"=>""
		);
// These are the listings of fields that require input from the user. Fail the insertion
// if we don't have all of these. The combination of required and non-required arrays MUST
// be equivalent in count and order to all_fields. Also, these two arrays hold the mappings
// between database column names and web page input field names.
$reqd_fields = array("userid"=>"uid",
                     "time_in"=>"start_time",
                     "time_out"=>"end_time",
                     "depth"=>"depth",
                );
// These are non-required fields. If we don't get a value for them, then we can just assign
// them nulls for the database insert.
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

	foreach( $all_fields as $col=>$val ){
		$web_val = isset($reqd_fields[$col]) ? $reqd_fields[$col] : $nonreqd_fields[$col];
		$stpl->assign($web_val,$divedata[$col]);
	}
	
	// the $coords got assigned in the loop above, but since there is nothing on the web page
	// to parse for it, we can ignore it.
	// here, we process the data and set it correctly.
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
	$stpl->assign('action', "update");
	$stpl->assign('diveid', $divedata['diveid']);
	
	$content = $stpl->fetch('diveedit.html');
	$stpl->assign('login', $session->getAuthStatus()? 'Logout':'Login');
	$stpl->assign('title', "Divelog: Editing dive");
	$stpl->assign('content',$content);
	$stpl->display('shell.html');
	die('');
}
else if( isset($_GET['action']) && strtoupper($_GET['action']) == 'ADD' ){
	$error = array();  // for collecting names of invalid fields
	
	$date = $_POST['Date_Year'].'-'.strtoupper(substr($_POST['Date_Month'],0,3)).'-'.$_POST['Date_Day'];
	//TODO - TASK start_time and end_time could be empty, check them
	if( !empty($_POST['start_time']) && !empty($_POST['end_time']) ){
		$_POST['start_time'] = $date .' '.$_POST['start_time'];
		$_POST['end_time']   = $date .' '.$_POST['end_time'];
	}
	
	if( empty($_POST['coord_lat']) || empty($_POST['coord_lon']) ){
		$_POST['coords'] = 'null';
	}
	else {
		$_POST['coords'] = $_POST['coord_lat'] .",". $_POST['coord_lon'];
		unset($_POST['coord_lat']);  // we dont need these confusing things any more
		unset($_POST['coord_lon']);
	}	

	$_POST = unslash_arr($_POST);
	// Begin our field processing. We'll try to do as much as we can in this loop and make
	// it as generic as possible so that it can be extended later if need be.
	// Basically, if we're gonna have to go through them all anyways,
	// we might as well check everything while we are at it.
	foreach( $all_fields as $col=>$x ){
		// get the matching web field name from the right array
		$val = isset($reqd_fields[$col]) ? $reqd_fields[$col] : $nonreqd_fields[$col];

		switch( $field_types[$col] ){
			case "integer":
				if( ctype_digit($_POST[$val]) ){
					$all_fields[$col] = $_POST[$val];
				}
				else{
					$error[$val] = true;
				}
				break;  //integers are not enclosed in quotes
			case "text":
				if( ctype_print($_POST[$val]) ){
					$all_fields[$col] = "'" . pg_escape_string($_POST[$val]) . "'";
				}
				else{
					$error[$val] = true;
				}
				break;
			case "boolean":
				if( ctype_digit($_POST[$val]) && ($_POST[$val] == true || $_POST[$val] == false) ){
					$all_fields[$col] = $_POST[$val];
				}
				else{
					$error[$val] = true;
				}
				break; //boolean doesn't need quotes
			case "point":
				$marker = strpos($_POST[$val],',');
				if( is_numeric(substr($_POST[$val],0,$marker)) && is_numeric(substr($_POST[$val],$marker+1)) ){
					$all_fields[$col] = "'" . $_POST[$val] . "'";
				}
				else if( $_POST[$val] == 'null' ){
					$all_fields[$col] = $_POST[$val];
				}
				else{
					$error[$val] = true;
				}
				unset($marker);
				break;
			case "timestamp":
				error_log('processing '.$col.'= '.$_POST[$val]);
				$theTime = array();
				if( preg_match('/(\\d){4}[-]([A-Z]){3}[-](\\d){1,2} (\\d){2}[:](\\d){2}[:\\d{2}]*/',$_POST[$val], $theTime) ){
					$all_fields[$col] = "'" . $theTime[0] . "'";
					//update $_POST field too, just in case
					$_POST[$val] = $theTime[0];
				}
				else{
					$error[$val] = true;
				}
				unset($theTime);
				break;
			case "interval":
				$theTime = array();
				if( preg_match('/(\\d){1,2}[:](\\d){1,2}/',$_POST[$val],$theTime) ){
					$all_fields[$col] = "'" . pg_escape_string($theTime[0]) . "'";
					$_POST[$val] = $theTime[0];
				}
				else if( empty($_POST[$val]) ){
					$all_fields[$col] = "null"; // intervals should be in quotes but a interval
								    // set to null should not have them.
				}
				else{
					$error[$val] = true;
				}
				unset($theTime);
				break;
			default:
				$error['msg'] = "Internal error: field '$val' does not have a valid type!";
		}// switch
		if( $all_fields[$col] == '' || $all_fields == "''" ){
			//check our required fields for values. If empty, put it in an array that will let us flag the
			// fields on the web page later on.
			if( isset($nonreqd_fields[$col]) ){
				$all_fields[$col] = preg_match("/^''$/",$all_fields[$col]) ? "'null'" : "null";
			}
			else{
				$error[$val] = true;
			}
		}
	}// foreach
	// now check for errors
	if( count($error) > 0 ){
		$stpl = new Smarty_divelog();
		$stpl->assign('error',$error);
		$stpl->assign('username', $_POST['username']);
		$stpl->assign('uid', $_POST['uid']);
		$stpl->assign('submit', "Add Dive");
		$stpl->assign('action', "add");
		foreach( $all_fields as $blah=>$data ){
			$field = (isset($reqd_fields[$blah])) ? $reqd_fields[$blah] : $nonreqd_fields[$blah];
			$stpl->assign($field, $_POST[$field]);
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
		$content = $stpl->fetch('diveedit.html');	

		$stpl->assign('title', "Divelog - Add dive");
		$stpl->assign('login', ($session->getAuthStatus())?"Logout" : "Login" );
		$stpl->assign('content',$content);
		$stpl->display('shell.html');

		die('');
	}

	//we need to build the query seperately
	$add_query = 'SELECT ins_dive(' . implode(',', $all_fields) . ')';
	/*print "<pre>";
	print_r($add_query);
	print "</pre>";
	die('');
	*/
	$db->queryResult($add_query);
	$add_attempt_message = $db->error;
	if (empty($add_attempt_message)) {
		$add_attempt_message = 'Dive successfuly added on';
		//build the text here
	}	
		
	$stpl = new Smarty_divelog();
	$stpl->assign('dive_add_message', $add_attempt_message);
	$stpl->assign('dive_date',$all_fields['start_time']);
	$stpl->assign('dive_desc',$all_fields['description']);

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
   $stpl->assign('username', $user['username']);
   $stpl->assign('uid', $user['userid']);
   $stpl->assign('submit', "Add Dive");
   $stpl->assign('action', "add");

   $content = $stpl->fetch('diveedit.html');
   $stpl->assign('title', "divelog entry for ". $user['username']);
   $stpl->assign('login', $session->getAuthStatus()? "Logout" : "Login" );
   $stpl->assign('content',$content);
   $stpl->display('shell.html');
}

?>
