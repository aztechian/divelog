<?PHP
include('common.php');
include('session.php');
global $session;

error_log(print_r($_POST,true));
if( isset($_POST['action']) && strtoupper($_POST['action']) == 'LOGIN' ){
        $userid = $db->queryResult("SELECT sel_user_creds('".addslashes($_POST['user'])."') AS password", 'password');

        $stpl = new Smarty_divelog();  //this will be needed regardless
	error_log("Got user credentials...");
        if (!empty($userid) && $userid == md5($_POST['pass'])) {

                $session->setAuthStatus(true);  //log them in

                $stpl->assign('name', addslashes($_POST['user']));
                $stpl->assign('sessionname', $divelog->config->siteconf['cookiename'].'_id');
                $stpl->assign('sid',$session->getID());
		if( isset($_POST['return']) && $_POST['return'] != "" ){ //return them to their page if they were
			error_log("Redirecting to previous page: ".$_POST['return'].".php");
			header("Location: " .$_POST['return']. ".php");  // on their way somewhere.
			die('');
		}
		else{
			error_log("Continue to the welcome page");
                	$content = $stpl->fetch('welcome.html');
		}
                $stpl->assign('title', "Welcome to divelog");
                $stpl->assign('login',"Logout");
                $stpl->assign('content',$content);
                $stpl->display('shell.html');
                die('');

        } else {
		error_log("Bad login");
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
else{   //no params, so just show the login page
	error_log("No params recognized, show login page");
	$stpl = new Smarty_divelog();
        //do some testing to make sure the db stuff is on
        if ($db->error) {
                //echo "We have an error " . $db->error;
                $stpl->assign('errordb', true);
                $stpl->assign('errortext', $db->error);
        }
	if( isset($_GET['return']) && $_GET['return'] != "" ){
		$stpl->assign('return', $_GET['return']);
	}
	$content = $stpl->fetch('index.html');

	$stpl->assign('title', "Divelog Login");
	$stpl->assign('content', $content);
	$stpl->assign('login', "Login");
	$stpl->display('shell.html');
	die('');
}
