<?PHP

include('common.php');

if( isset($_POST['action']) AND strtoupper($_POST['action']) == "LOGIN" ){

   $userid = $db->getAllRows("SELECT userid,password FROM users WHERE username='".addslashes($_POST['user'])."'" );
   
   if( $userid[0]['password'] == md5($_POST['pass']) ){
	$stpl = new Smarty_divelog();
        $stpl->assign('title', "Divelog Login");
	$stpl->assign('name',$_POST['user']);
	$stpl->assign('uid',$userid[0]['userid']);
	$stpl->assign('lastlogin',time());

	$db->getAllRows("UPDATE users SET lastvisit='NOW', loggedin='true' WHERE userid='".$userid[0]['userid']."'");

	$stpl->display('welcome.html');
	die('');
   }
   else{
	$stpl = new Smarty_divelog();
        $stpl->assign('title', "Divelog Login");
	$stpl->display('index.html');
	die('');
   }
}
else{
   $stpl = new Smarty_divelog();
   $stpl->assign('title', "Divelog Login");
   $stpl->display('index.html');
}
?>
