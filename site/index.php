<?PHP
include ('common.php');

if (isset ($_POST['action']) AND strtoupper($_POST['action']) == "LOGIN") {

	$userid = $db->getSingleRow("SELECT userid,password FROM users WHERE username='".addslashes($_POST['user'])."'");

	if ($userid[0]['password'] == md5($_POST['pass'])) {
		$db->fullQuery("UPDATE users SET lastvisit='NOW', loggedin='true' WHERE userid='".$userid[0]['userid']."'");
		header('Location: welcome.php');
		die('');
	} else {
		header('Location: index.php');
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

