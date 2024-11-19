<?php
require_once __DIR__ . "/../config/database.php";

class Database {
	public static function getCursor() {
		$db = new PDO(
	    	"mysql:host=" . DB_HOST . ";port=3306;dbname=" . DB_NAME, 
	    	DB_USER, 
	    	DB_PASSWORD, 
	    	array(
		    	PDO::ATTR_PERSISTENT => true
			)
	    );

	    // set the PDO error mode to exception
	    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	    return $db;
	}
}