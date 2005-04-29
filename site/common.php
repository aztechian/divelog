<?PHP

define('SMARTY_DIR', '/usr/local/lib/php/Smarty/');
require(SMARTY_DIR . 'Smarty.class.php');

class Smarty_divelog extends Smarty{
    function Smarty_divelog(){
	$this->Smarty();
	$this->template_dir = '/var/www/Smarty/divelog/templates/';
	$this->compile_dir  = '/var/www/Smarty/divelog/templates_c/';
	$this->config_dir   = '/var/www/Smarty/divelog/config/';
	$this->cache_dir    = '/var/www/Smarty/divelog/cache/';

	$this->assign('app_name', 'divelog');
    }
} //End of class Smarty_divelog


class pg_db{
  var $handle;  //database handle for use in functions

/*===================================================
 * FUNCTION: pg_db class constructor
 *
 *===================================================
 */
  function pg_db(){
	
        include('config.php');
	$db = pg_connect("host=".$dbconf['host'].
			 " port=".$dbconf['port'].
			 " user=".$dbconf['name'].
			 " password=".$dbconf['pass'].
			 " dbname=".$dbconf['db'] ); 
	   
//	   return false;
	   $this->handle = $db;
   }
/*===================================================
 * FUNCTION: getAllRows()
 *
 *===================================================
 */
   function getAllRows($qstr){
        include('config.php');
	if( 0 === pg_connection_status($this->handle) ){
	    $results = pg_query($this->handle, $qstr);
	    if (!$results) {
		echo "An error occured.\n";
  		exit;
	    }
	    $rows = pg_fetch_all($results);
	    return $rows;
	}
	else{
	    die("Connection to postgreSQL (". $dbconf['host'].") invalid --> ".
		pg_last_error($this->handle). "\n"
	       );
	}
   }
}//end of class pg_db

$db = new pg_db();

 
?>
