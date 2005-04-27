<?PHP
/*
 *          divelog Installation script
 *                   Ian Martin
 *                 April 27, 2005
 *
 *   This script will install the database components
 *   needed to use divelog. This should be run from a
 *   web browser, NOT from the command line.
 *
 */

if( isset($_POST['action']) && strtoupper($_POST['action']) == "INSTALL" ){


   $conn_str[] = "host="     . (isset($_POST['host']))    ? $_POST['host']    : 'localhost';
   $conn_str[] = "port="     . (isset($_POST['port']))    ? $_POST['port']    : '5432';
   $conn_str[] = "user="     . (isset($_POST['pgadmin'])) ? $_POST['pgadmin'] : 'postgres';
   $conn_str[] = "password=" . (isset($_POST['pgpass']))  ? $_POST['pgpass']  : '';
   $conn_str[] = "dbname="   . "template1"; //must use this for initial connection

   if( !$db = pg_connect( stripslashes(implode(" ", $conn_str)) ) ){
	$error[] = 'Error connecting to database: Ensure you have entered the correct parameters';
        failure();
   }
   if( !$fh = fopen('ddl/divelog.ddl', 'r') ){
	$error[] = 'Error during installation: Could not open DDL file!';
 	failure();
   }
   $ddl_contents = fread($fh);
   $sql = explode(';', $ddl_contents);

   unset($ddl_contents);
   foreach( $sql as $stmt ){
	if( !($result = $pg_query($db, $stmt)) ){
	   $error[] = 'Error during installation: ' . pg_last_error($db);
	   failure();
        }
   }

   pg_close($db);
}
else{
   ?>
<HTML>
    <HEAD><title>divelog installation</title></HEAD>
    <BODY>
	<form name="install" action="install.php" method="POST">
	   <H3>Username: </H3><input type="text" name="pgadmin" value=""><br>
           <H3>Password: </H3><input type="password" name="pgpass" value=""><br>
           <H3>Server:   </H3><input type="text" name="host" value="localhost"><br>
           <H3>Port: </H3>    <input type="text" name="port" value="5432" size="5"><br>
           <H3>Database: </H3><input type="text" name="dbname" value="divelog"><br>
	   <br><br>
	   <input type="submit" name="action" value="Install">
	   <input type="reset" name="cancel" value="Cancel" onClick="history.back();">
	</form>
    </BODY>
</HTML>
<?
}
function failure(){
   global $error;
   print implode("\n<br>", $error);
   die('');
}
?>
