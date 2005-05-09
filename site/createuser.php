<?PHP

include('common.php');

$nonreqd = array("location","timezone");
$required = array("username","password","fname","lname","email");
$errors = array();
$tzs = array("-12","-11","-10","-09","-08","-07","-06","-05","-04","-03","-02","-01","0","01","02","03","04","05","06","07","08","09","10","11","12");


if( isset($_POST['action']) && strtoupper($_POST['action']) == "NEWUSER" ){
	foreach( $required as $f ){
		if( !isset($_POST[$f]) )
		   $errors[] = $f;
	}
	if( count($errors) > 0 ){
		$name_exists = $db->queryResult("SELECT sel_user_exists(".addslashes($_POST['username'])."') AS exists",'exists');
		
		$stpl = new Smarty_divelog();
		$stpl->assign('title',"Divelog Create New Account");
		$stpl->assign('name_exists',$name_exists);
		$stpl->assign('badfields',$errors);
		$stpl->assign('values',$_POST);
		$content = $stpl->fetch('createuser.html');
		$stpl->assign('content',$content);
		$stpl->display('shell.html');

		die('');
	}
	//If we get past here, we should have fields filled in.

	array_walk($_POST, 'slash');	//addslashes to the whole POST array now

	$sql = "SELECT ins_user(" .
	$sqlvals = '';
	foreach( $required as $f ){
		$sqlvals[] = "'".$_POST[$f]."'";
	};
	foreach( $nonreqd as $f ){
		$str = (empty($_POST[$f])) ? "null" : $_POST[$f];
		$sqlvals[] = "'$str'";
	}

	$sql .= implode(",", $sqlvals);
	$sql .= ") AS userid";

	$uid = $db->queryResult($sql,'userid');

	if( !empty($uid) ){
		$stpl = new Smarty_divelog();
		$stpl->assign('title', "Divelog user created");
		$userinfo = $db->queryRow("SELECT * FROM users WHERE userid=".$uid);
		$stpl->assign('userdata',$userinfo);
		$content = $stpl->fetch('validuser.html');

		$stpl->assign('content',$content);
		$stpl->display('shell.html');
	}
	else{
		print "Error creating user!";
		print_r($sql);
		die('');
	}

}
else{
	$stpl = new Smarty_divelog();
	$stpl->assign('title', "Divelog: Create new user");
	$stpl->assign('timezones',$tzs);
	$content = $stpl->fetch('createuser.html');
	
	$stpl->assign('content',$content);
	$stpl->display('shell.html');
	die('');
}

function slash( $string ){
	if( !get_magic_quotes_gpc() ){
		$string = addslashes($string);
	}
	return $string;
}
