<?PHP
include_once ('config.php');
class Session {
	var $sessionid;

	function Session($siteconfig, $ses = '') {
		
		if(is_array($siteconfig)) {
			$siteconf = $siteconfig;
		}
		else {
			echo 'Unable to instantiate Session object, config is misisng';
			//exit here
			return null;
		}
		
		if (empty($ses)) {

			if (isset($_COOKIE[$siteconf['cookiename'] . '_id'])) {
				$this->sessionid = $_COOKIE[$siteconf['cookiename'] . '_id'];
			}
			else if (isset ($_GET[$siteconf['cookiename'].'_id'])) {
				$this->sessionid = $_GET[$siteconf['cookiename'].'_id'];
			}
			else if (isset ($_POST[$siteconf['cookiename'].'_id'])) {
				$this->sessionid = $_POST[$siteconf['cookiename'].'_id'];
			}
			
			//whatever the case may be if I have an empty session create a new one
			
			if ($this->sessionid == '') {
				//brand new session
				$this->sessionid = md5(uniqid(rand(), true));
				//a second test might be useful if md5 fails ...
			}
			
			if(setcookie($siteconf['cookiename'].'_id', $this->sessionid, 0, $siteconf['install_path'], $siteconf['domain'])) {
				//echo 'Cookie set with value (' . $this->sessionid . ')';
			}
			else {
				//echo 'Cookie NOT set';
				return null;
			}
			//$_COOKIE[$siteconf['cookiename'].'_id'] = $this->sessionid;
			//forced add -- not a good idea

		} 
		else {
			//at least here we have a session ... if we cannot set the cookie we cannot really stay logged in
			$this->sessionid = $ses;
			setcookie($siteconf['cookiename'].'_id', $this->sessionid, false, $siteconf['install_path'], $siteconf['domain'], false);
			//$_COOKIE[$siteconf['cookiename'].'_id'] = $this->sessionid;
			//forced add -- not a good idea
		}
	}

	function update($siteconfig) {
		
		if(is_array($siteconfig)) {
			$siteconf = $siteconfig;
		}
		else {
			echo 'Unable to instantiate Session object, config is misisng';
			//exit here
			return null;
		}
		
		$update = $db->queryResult("SELECT upd_session($this->sessionid) AS sid", 'sid');
		if (!$update) { //if this comes back false, they gave us a bad sessionid. Shame on them. We'll make them a new one.
			$this->sessionid = md5(uniqid(rand(), true));
			setcookie($siteconf['cookiename'].'_id', $this->sessionid, false, $siteconf['install_path'], $siteconf['domain'], false);
			$_COOKIE[$siteconf['cookiename'].'_id'] = $this->sessionid;
		}
		return true;
	}

	function destroy($uid = '') {
		$result = $db->queryResult("SELECT do_user_logout(".$uid.") AS done", 'done');
		return true;
	}
	
	function getID(){
		return $this->sessionid;
	}
}

$session =& new Session($siteconf, $_GET[$siteconf['cookiename'].'_id']);
//might want to check and see if we have a session at all ..

?>
