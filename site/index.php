<?PHP
include ('common.php');
include('session.php');
global $session;
global $divelog;

if (isset ($_POST['action']) && strtoupper($_POST['action']) == "LOGIN") {

	$userid = $db->queryResult("SELECT sel_user_creds('".addslashes($_POST['user'])."') AS password", 'password');

	$stpl = new Smarty_divelog();  //this will be needed regardless
	if (!empty($userid) && $userid == md5($_POST['pass'])) {
		include_once('session.php');
		
		$db->queryResult("SELECT ins_session('".$session->getID()."') AS nothing", 'nothing');
		
		$sql = "SELECT do_user_login('".
				addslashes($_POST['user'])."','".
				addslashes($_SERVER['REMOTE_ADDR'])."','".
				$session->getID()."') AS sessionid
				";
		$sessid = $db->queryResult($sql, 'sessionid' );

		$stpl->assign('name', addslashes($_POST['user']));
		$stpl->assign('sessionname', $divelog->config->siteconf['cookiename'].'_id');
		$stpl->assign('sid',$session->getID());
		$content = $stpl->fetch('welcome.html');

		$stpl->assign('title', "Welcome to divelog");
		$stpl->assign('login',"Logout");
		$stpl->assign('content',$content);
		$stpl->display('shell.html');
		die('');
	} else {
		$stpl->assign('login', false);
		$stpl->assign('badlogin',true);
		$content = $stpl->fetch('index.html');

		$stpl->assign('title', "Divelog Login");
		$stpl->assign('login', "Login");
		$stpl->assign('content', $content);
		$stpl->display('shell.html');
		die('');
	}
} 
else if(isset ($_GET['action']) && strtoupper($_GET['action']) == "LOGOUT" ){
	$uid = $session->getID();
	if( isset($uid) && $uid == "" ){
		header("Location: index.php");
		die('');
	}
	$session->destroy($uid);
	header("Location: index.php");
	die('');
}
else {
	$stpl = new Smarty_divelog();
	//do some testing to make sure the db stuff is on
	if ($db->error) {
		//echo "We have an error " . $db->error;
		$stpl->assign('errordb', true);
		$stpl->assign('errortext', $db->error);
	}
	$content = $stpl->fetch('index.html'); //get and parse the main "content" of the page

	$stpl->assign('title', "Divelog Login");
	$stpl->assign('login', "Login");
	$stpl->assign('content', $content); //this puts the "content" of the page into the main layout template
	$stpl->display('shell.html');
}
?>

