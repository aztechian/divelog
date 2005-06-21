<?PHP

include('common.php');
include('session.php');

if( !$session->getAuthStatus() ){
	header("Location: login.php?return=recent");
	die('');
}

if( isset($_POST['action']) && strtoupper($_POST['action']) == 'REMOVE' ){

}
else if( isset($_POST['action']) && strtoupper($_POST['action']) == 'ADD' ){
	header("Location: diveedit.php");
}
else{
	$uname = $db->queryRow("SELECT * FROM sel_user_from_sessid('". $session->getID() . "') AS (userid int, username text)" );

	$dives = $db->queryAllRows(
			"SELECT diveid,time_in, description, depth, location_city || ', ' || location_state AS location ".
			"FROM sel_dive_data(".$uname['userid'].")"
		);

	$stpl = new Smarty_divelog();
	$stpl->assign('data', $dives);
	$stpl->assign('records', count($dives) );
	$stpl->assign('name', $uname['username']);
	
	$content = $stpl->fetch('divelist.html');
	$stpl->assign('content', $content);
	$stpl->assign('title', $uname['username']."'s Recent Dives");
	$stpl->assign('login', $session->getAuthStatus()? 'Logout' : 'Login');
	$stpl->display('shell.html');

	die('');
}

