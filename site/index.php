<?PHP
include ('common.php');
include ('session.php');
//global $session;
global $divelog;

if (isset ($_POST['action']) && strtoupper($_POST['action']) == "LOGIN") {
	header("Location: login.php");
	die('');
} 
else if(isset ($_GET['action']) && strtoupper($_GET['action']) == "LOGOUT" ){
	header("Location: logout.php");
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
	if ($session->getAuthStatus()) {
		//I am logged in
		//get the name
		
		$uname = $db->queryRow("SELECT * FROM sel_user_from_sessid('". $session->getID() . "') AS (userid int, username text)" );
		$stpl->assign('name', $uname['username']);
		$stpl->assign('login', "Logout");
		$content = $stpl->fetch('index-in.html');
	}
	else {
		//I am new
		$stpl->assign('login', "Login");
		$content = $stpl->fetch('index.html'); //get and parse the main "content" of the page
	}

	$stpl->assign('title', "Divelog Login");	
	$stpl->assign('content', $content); //this puts the "content" of the page into the main layout template
	$stpl->display('shell.html');
}
?>

