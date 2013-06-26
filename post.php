<?
	
	error_reporting(E_NONE);
	
	require_once('config.php');
	
	$post = $_POST['data'];
	
	$data = json_decode($post);
	
	$M = mysql_connect($db_server, $db_user, $db_password) or die('Not connected to database');
	mysql_select_db($db_name, $M) or die('Not selected database');
	mysql_query("INSERT INTO sessions VALUES(0,1)", $M);
	
	$query = mysql_query('SELECT LAST_INSERT_ID() as id;', $M);
	
	$row = mysql_fetch_array($query);
	$result = array();
	$result["id"] = $row["id"];
	
	echo json_encode($result);
	
	mysql_close($M);	
	
?>