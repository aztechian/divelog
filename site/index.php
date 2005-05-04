<?PHP
include ('common.php');

if (isset ($_POST['action']) AND strtoupper($_POST['action']) == "LOGIN") {

	$userid = $db->queryResult("SELECT sel_user_creds('".addslashes($_POST['user'])."') AS password", 'password');

	$stpl = new Smarty_divelog();  //this will be needed regardless
	if ($userid == md5($_POST['pass'])) {
		$sessid = $db->queryResult("SELECT do_user_login(".addslashes($_POST['user']).",'".addslashes($_REQUEST['REMOTE_ADDR'])."') AS sessionid", 'sessionid' );

		$stpl->assign('title', "Welcome to divelog");
		$stpl->assign('user', $sessid);
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
} else {
	$stpl = new Smarty_divelog();
	$stpl->assign('title', "Divelog Login");
	$content = $stpl->fetch('index.html'); //get and parse the main "content" of the page

	$stpl->assign('content', $content); //this puts the "content" of the page into the main layout template
	$stpl->display('shell.html');
}
?>

