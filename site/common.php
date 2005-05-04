<?PHP
define('SMARTY_DIR', '/usr/local/lib/php/Smarty/');
require (SMARTY_DIR.'Smarty.class.php');

class Smarty_divelog extends Smarty {
	/* This class is for using the Smarty template system.
	 * By setting our variables that are unique to this application
	 * here, we can easily use Smarty templates for any page in
	 * the app.
	 *
	 * To use, just include this file in the PHP file you want to use
	 * a template with, and instantiate a new "Smarty_divelog" object.
	 * EXAMPLE:
	 *    include('common.php');
	 *    $stpl = new Smarty_divelog();
	 *    $stpl->display('mypage.html');
	 *
	 * Taken from the Smarty documentation. http://smarty.php.net
	 */
	function Smarty_divelog() {
		$this->Smarty();
		$this->template_dir = '/var/www/Smarty/divelog/templates/';
		$this->compile_dir = '/var/www/Smarty/divelog/templates_c/';
		$this->config_dir = '/var/www/Smarty/divelog/config/';
		$this->cache_dir = '/var/www/Smarty/divelog/cache/';

		$this->assign('app_name', 'divelog');
	}
} //End of class Smarty_divelog

class pg_db {
	private $handle = false; //database handle for use in functions
	private $resultSet; //used to hold temporary results (see getRow)
	private $row;
	private $rows = array ();

	function pg_db() {
		/*========================================================================
		 * FUNCTION: pg_db class constructor
		 *
		 *         Input:  None
		 *        Output:  None
		 *       Returns:  A new pg_db object
		 *   Limitations:  Does not initialize the class variables
		 *========================================================================
		 */
	}

	function connect() {
		/*========================================================================
		 * FUNCTION: connect()
		 *
		 *         Input:  None
		 *        Output:  None if successful. Upon failure an error
		 *                 message will be echoed.
		 *       Returns:  a boolean that represents the state
		 *                 of the connection to the database.
		 *                 true is success. false is failure. 
		 *   Limitations:  None
		 *========================================================================
		 */
		include ('config.php');
		$db = pg_connect("host=".$dbconf['host']." port=".$dbconf['port']." user=".$dbconf['name']." password=".$dbconf['pass']." dbname=".$dbconf['db']);

		if (!$db) {
			return $this->problem("Could not connect to database! Check configuration.");
		}
		$this->handle = $db;
		return true;
	}

	function query($qstr) {
		/*========================================================================
		 * FUNCTION: query()
		 *
		 *         Input:  a string that holds a valid postgreSQL SQL query.
		 *        Output:  None on successful query execution. Upon failure of
		 *                 query execution, appropriate error messages will be echoed.
		 *       Returns:  A boolean that represents the success of execution of the
		 *                 given query string.
		 *                 true is success. false is failure.
		 *   Limitations:  Error messages may not correspond to the actual error.
		 *                 See the PHP documentation for the pg_last_error function
		 *                 for details.
		 *========================================================================
		 */
		if (!$this->handle) {
			return $this->problem("No database connection for query.");
		} else {
			$this->resultSet = pg_query($qstr);
			if (!$this->resultSet) {
				return $this->problem("Error during query.");
			}
			return true;
		}
	}

	function rows() {
		/*========================================================================
		 * FUNCTION: rows()
		 *
		 *  Precondition:  A result set must exist (ie. a call to query())
		 *         Input:  None, takes existing result set
		 *        Output:  Upon failure, appropriate error messages are echoed.
		 *       Returns:  Mixed. Returns an integer of the number of rows in the
		 *                 result set if successful. A failure will return a boolean
		 *                 of false.
		 *   Limitations:  None.
		 *========================================================================
		 */
		if ($this->resultSet) {
			return pg_num_rows($this->resultSet);
		} else {
			return $this->problem("Can't give number of rows without a result set");
		}
	}

	function cols() {
		/*========================================================================
		 * FUNCTION: cols()
		 *
		 *  Precondition:  A result set must exist (ie. a call to query())
		 *         Input:  None, takes existing result set.
		 *        Output:  Upon failure, appropriate error messages are echoed.
		 *       Returns:  Mixed. Returns an integer of the number of columns in the
		 *                 result set if successful. A failure will return a boolean
		 *                 of false.
		 *   Limitations:  None.
		 *========================================================================
		 */
		if ($this->resultSet) {
			return pg_num_cols($this->resultSet);
		} else {
			return $this->problem("Can't give number of columns without a result set");
		}
	}

	function getAllRows() {
		/*========================================================================
		 * FUNCTION: getAllRows()
		 *
		 *  Precondition:  A result set must exist (ie. a call to query())
		 *         Input:  None, takes existing result set.
		 *        Output:  Echoes error messages upon failure.
		 *       Returns:  An array holding the rows of the previous query results.
		 *                 Returns a boolean of false upon failure.
		 *   Limitations:  None.
		 *========================================================================
		 */
		if ($this->resultSet) {
			$this->rows = NULL;
			$tempRow = '';
			while ($tempRow = pg_fetch_array($this->resultSet)) {
				$this->rows[] = $tempRow;
			}
			return $this->rows;
		} else {
			return $this->problem("Can't get all rows without a result set");
		}
	}

	function getSingleRow() {
		/*========================================================================
		 * FUNCTION: getSingleRow()
		 *
		 *  Precondition:  A result set must exist (ie. a call to query())
		 *         Input:  None, takes existing result set.
		 *        Output:  Echoes error messages upon failure.
		 *       Returns:  An associative array holding the values of one row (the
		 *                 next in the result set). Returns a boolean value of 
		 *                 false upon failure.
		 *   Limitations:  None.
		 *========================================================================
		 */
		if ($this->resultSet) {
			$this->row = pg_fetch_array($this->resultSet);
			return $this->row;
		} else {
			return $this->problem("Can't get row without a result set");
		}
	}

	function finish() {
		/*========================================================================
		* FUNCTION: finish()
		*
		*  Precondition:  A result set must exist (ie. a call to query())
		*         Input:  None, takes existing result set.
		*        Output:  None
		*       Returns:  A boolean indicating the success of freeing memory taken
		*                 by the most recent result set.
		*   Limitations:  None.
		*========================================================================
		*/
		return pg_free_result($this->resultSet);
	}

	function queryAllRows($qstr) {
		/*========================================================================
		* FUNCTION: queryAllRows($qstr)
		*
		*         Input:  A string holding the postgreSQL SQL query to be executed.
		*        Output:  None
		*       Returns:  An array containing the rows of the result set from the
		*                 given query.
		*   Limitations:  None
		*========================================================================
		*/
		$this->query($qstr);
		$tempSet = $this->getAllRows();
		pg_free_result($this->resultSet);
		return $tempSet;
	}

	function queryRow($qstr) {
		/*========================================================================
		* FUNCTION: queryRow($qstr)
		*
		*         Input:  A string containing the postgreSQL SQL query to execute.
		*        Output:  None
		*       Returns:  An associative array holding the values of a single row
		*                 (the next in the result set) from the results of the 
		*                 query passed in.
		*   Limitations:  None
		*========================================================================
		*/
		$this->query($qstr);
		$tempSet = $this->getSingleRow();
		pg_free_result($this->resultSet);
		return $tempSet;
	}

	function fullQuery($qstr) {
		/*========================================================================
		* FUNCTION: fullQuery($qstr)
		*
		*         Input:  A string containing a postgreSQL SQL query to execute.
		*        Output:  None
		*       Returns:  A boolean containing the status of freeing the memory
		*                 taken by the result set from the query that was passed in.
		*   Limitations:  Does not return the status of the query itself.
		*========================================================================
		*/
		$this->query($qstr);
		return pg_free_result($this->resultSet);
	}

	function problem($txt) {
		/*========================================================================
		* FUNCTION: $this->problem()
		*
		*         Input:  A string holding the error message to be printed.
		*        Output:  The given string parameter plus any error messages that
		*                 can be had from pg_last_error.
		*       Returns:  a boolean of false. This function is called in an error
		*                 state, thus it always returns false to indicate this
		*                 and for ease of use by the calling function. This
		*                 function can be returned directly from the calling function.
		*   Limitations:  Always returns false.
		*========================================================================
		*/
		echo pg_last_error()."\n";
		echo "$txt"."\n\n";
		return false;
	}

} //end of class pg_db

$db = new pg_db(); //instantiate new db object for simplicity of "include-er"
$db->connect();
?>



