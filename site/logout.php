<?PHP

include('session.php');
global $session;

$uid = $session->getID();

if( isset($uid) && $uid != "" ){
	
	if ($session->destroy($uid)) {
		header("Location: index.php");
	}
	else {
		//error destroying the session ...
		echo 'logout';
		echo 'DB exception: (' . $db->error . ')';
	}
}

die('');
