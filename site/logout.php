<?PHP

include('session.php');
global $session;

$uid = $session->getID();
if( isset($uid) && $uid != "" ){
	$session->destroy($uid);
}
header("Location: index.php");
die('');
