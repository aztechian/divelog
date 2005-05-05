<?PHP
include_once ('config.php');
class Session{
	var $sessionid;

	function Session($ses = '') {
		if (empty($ses)) {
			if (isset ($_COOKIE[$siteconf['cookiename'].'_id']) or isset ($_GET[$siteconf['cookiename'].'_id']) or isset ($_POST[$siteconf['cookiename'].'_id'])) {
				$this->sessionid = isset ($_COOKIE[$siteconf['cookiename'].'_id']) ? $_COOKIE[$siteconf['cookiename'].'_id'] : isset ($_GET[$siteconf['cookiename'].'_id']) ? $_GET[$siteconf['cookiename'].'_id'] : isset ($_POST[$siteconf['cookiename'].'_id']) ? $_POST[$siteconf['cookiename'].'_id'] : '';
				if ($this->sessionid == '') {
					$this->sessionid = md5(uniqid(rand(), true));
				}
			} 
			else {
				$this->sessionid = md5(uniqid(rand(), true));
			}
			setcookie($siteconf['cookiename'].'_id', $this->sessionid, $siteconf['install_path'], $siteconf['domain'], false);
			$_COOKIE[$siteconf['cookiename'].'_id'] = $this->sessionid;
		} 
		else {
			$this->sessionid = $ses;
			setcookie($siteconf['cookiename'].'_id', $this->sessionid, $siteconf['install_path'], $siteconf['domain'], false);
			$_COOKIE[$siteconf['cookiename'].'_id'] = $this->sessionid;
		}
	}

	function update() {
		$update = $db->queryResult("SELECT upd_session($this->sessionid) AS sid", 'sid');
		if (!$update) { //if this comes back false, they gave us a bad sessionid. Shame on them. We'll make them a new one.
			$this->sessionid = md5(uniqid(rand(), true));
			setcookie($siteconf['cookiename'].'_id', $this->sessionid, $siteconf['install_path'], $siteconf['domain'], false);
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

$session =& new Session($_GET[$siteconf['cookiename'].'_id']);

?>