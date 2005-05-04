<?PHP

include('common.php');

if( isset($_POST['action']) && strtoupper($_POST['action']) == 'UPDATE' ){

}
else if( isset($_POST['action']) && strtoupper($_POST['action']) == 'DELETE' ){

}
else{  //default to a new record

   $stpl = new Smarty_divelog();
   $user = $db->getUserData();
   $stpl->assign('title', "divelog entry for $user['username']");

   $stpl->assign('username', $user['username']);
   $stpl->assign('uid', $user['userid']);
   
   $stpl->display('diveedit.html');
}

