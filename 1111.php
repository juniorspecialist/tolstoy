<?php

echo 'result='.iconv( 'utf-8' , 'windows-1252//IGNORE' , 'Базис Корп'); die();

$start = microtime(true);
error_reporting(E_ALL | E_STRICT) ;
ini_set('display_errors', 'On');
set_time_limit(0);
ini_set('memory_limit', '512M'); // 128 по-моему не хватало
ini_set('pcre.backtrack_limit', '5000000'); // 1000000 было мало
//echo phpinfo(); die();
/*
$mongo = new Mongo('localhost');
$database = $mongo -> test;
$item = array(
    "id"    => "ololo"
);
//$database -> items -> insert($item);
//$cursor = $database -> items -> find( array("id" => "ololo") );
$cursor = $database -> items -> find( );
foreach($cursor as $item){
    var_dump($item);
}*/
/*
try {
    // open connection to MongoDB server
    $conn = new Mongo('localhost');

    // access database
    $db = $conn->test;

    // access collection
    $collection = $db->items;

        for($i=0;$i<1000000;$i++){
            // insert a new document
            $item = array(
                'name' => md5(time()),
                'rec_id' => $i,
        );
        $collection->insert($item);
    }
    // disconnect from server
    $conn->close();
} catch (MongoConnectionException $e) {
    die('Error connecting to MongoDB server');
} catch (MongoException $e) {
    die('Error: ' . $e->getMessage());
}*/

//try {
//    // open connection to MongoDB server
//    $conn = new Mongo('localhost');
//
//    // access database
//    $db = $conn->test;
//
//    // access collection
//    $collection = $db->items;
//
//    // execute query
//    // retrieve all documents
//    $cursor = $collection->find();
//
//    // iterate through the result set
//    // print each document
//    echo $cursor->count() . ' document(s) found. <br/>';
//
//    foreach ($cursor as $obj) {
//        echo '<pre>'; print_r($obj);
//        echo '<br/>';
//    }
//
//
//    // disconnect from server
//    $conn->close();
//} catch (MongoConnectionException $e) {
//    die('Error connecting to MongoDB server');
//} catch (MongoException $e) {
//    die('Error: ' . $e->getMessage());
//}

$hostdb = 'localhost';
$namedb = 'mongo_test';
$userdb = 'root';
$passdb = 'root';

try {
    // Connect and create the PDO object
    $conn = new PDO("mysql:host=$hostdb; dbname=$namedb", $userdb, $passdb);
    $conn->exec("SET CHARACTER SET utf8");      // Sets encoding UTF-8

    for($i=0;$i<1000000;$i++){
            // Define an insert query
            $stmt = $conn->prepare("INSERT INTO test (hash, time_t) VALUES (?, ?)");
            $stmt->bindParam(1, $name);
            $stmt->bindParam(2, $value);

            // insert one row
            $name = md5(time());
            $value = time();
            $stmt->execute();
    }



    $conn = null;        // Disconnect

}
catch(PDOException $e) {
    echo $e->getMessage();
}

echo '<br>';
$time_end = microtime(true);
$time = $time_end - $start;
//echo $time;
printf('Скрипт выполнялся %.4F сек.', $time);

?>