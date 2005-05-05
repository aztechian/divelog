<?PHP
include ('common.php');

if (isset ($_POST['action']) AND strtoupper($_POST['action']) == "LOGIN") {

	$userid = $db->queryResult("SELECT sel_user_creds('".addslashes($_POST['user'])."') AS password", 'password');

	$stpl = new Smarty_divelog();  //this will be needed regardless
	if ($userid == md5($_POST['pass'])) {
		include_once('session.php');
		
		$db->queryResult("SELECT ins_session('".$session->getID()."') AS nothing", 'nothing');
		
		$sql = "SELECT do_user_login('".
				addslashes($_POST['user'])."','".
				addslashes($_REQUEST['REMOTE_ADDR'])."','".
				$session->getID()."') AS sessionid
				";
		$sessid = $db->queryResult($sql, 'sessionid' );

		$stpl->assign('title', "Welcome to divelog");
		$stpl->assign('name', addslashes($_POST['user']));
		$stpl->assign('sessionname', $siteconf['sessionname'].'_id');
		$stpl->assign('sid',$session->getID());
		$content = $stpl->fetch('welcome.html');

		$stpl->assign('content',$content);
		$stpl->display('shell.html');
		die('');
	} else {
		$stpl->assign('title', "Divelog Login");
		$stpl->assign('login', false);
		$content = $stpl->fetch('index.html');
		$stpl->assign('content', $content);
		$stpl->display('shell.html');
		die('');
	}
} 
else if(isset ($_GET['action']) AND strtoupper($_GET['action']) == "LOGOUT" ){
	
}
else {
	$stpl = new Smarty_divelog();
	$stpl->assign('title', "Divelog Login");
	$content = $stpl->fetch('index.html'); //get and parse the main "content" of the page

	$stpl->assign('content', $content); //this puts the "content" of the page into the main layout template
	$stpl->display('shell.html');
}
?>

