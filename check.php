<?
	
	error_reporting(E_NONE);
	
	$result = array("ok","ok", "ok", "ok", "ok", "ok", "ok", "ok",  "error", "in_progress");
	$msg = array("","","","","","","","", "some error", "");
	$proposed = array("","","","","","","","", "error description", "");
	
	
	$i = mt_rand(0, sizeof($result)-1);
	
	$res = array();
	$res["result"] = $result[$i];
	$res["msg"] = $msg[$i];
	$res["proposed"] = $proposed[$i];
	
	echo json_encode($res);
	
?>