<?PHP

include('common.php');

$nonreqd = array("location","timezone");
$required = array("username","password","fname","lname","email");
$errors = array();
$tzs = array("-12","-11","-10","-09","-08","-07","-06","-05","-04","-03","-02","-01","0","01","02","03","04","05","06","07","08","09","10","11","12");


if( isset($_POST['action']) && strtoupper($_POST['action']) == "NEWUSER" ){
	foreach( $required as $f ){
		if( empty($_POST[$f]) )
		   $errors[$f] = true;
	}
	
	$name_exists = $db->queryResult("SELECT sel_user_exists('".addslashes($_POST['username'])."')");
	if( $name_exists ){
		$errors['username'] = true;
	}
	if( count($errors) > 0 ){  //take care of error situations here
		
		$stpl = new Smarty_divelog();
		$stpl->assign('name_exists',$name_exists);
		$stpl->assign('badfields',$errors);
		$stpl->assign('values',$_POST);
		$content = $stpl->fetch('createuser.html');
		
		$stpl->assign('login','Login');
		$stpl->assign('title',"Divelog Create New Account");
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
		if( $f == 'timezone'){
			$sqlvals[] = (empty($_POST[$f])) ? "null" : $_POST[$f];
		}
		else{
			$str = (empty($_POST[$f])) ? "null" : $_POST[$f];
			$sqlvals[] = "'$str'";
		}
	}

	$sql .= implode(",", $sqlvals);
	$sql .= ")";
	$uid = $db->queryResult($sql);

	if( empty($uid) ){
		print "Error creating user!<br><pre>";
		print "\nQuery: ";
		print_r($sql);
		print "\nUserid value: ";
		print_r($uid);
		print "\nPOST array: ";
		print_r($_POST);
		print "</pre>";
		die('');
	}
	else{
		$stpl = new Smarty_divelog();
		$stpl->assign('title', "Divelog user created");
		$userinfo = $db->queryRow("SELECT * FROM users WHERE userid=".$uid);
		$stpl->assign('userdata',$userinfo);
		$content = $stpl->fetch('validuser.html');

		$stpl->assign('login','Login');
		$stpl->assign('content',$content);
		$stpl->display('shell.html');
	}

}
else{
	$stpl = new Smarty_divelog();
	$stpl->assign('title', "Divelog: Create new user");
	$stpl->assign('timezones',$tzs);
	$content = $stpl->fetch('createuser.html');
	
	$stpl->assign('login','Login');
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
