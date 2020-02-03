<?php
 
class DB {

	public static $db;
	public static $config = [];

	function __construct (){ $this->connect(); }

    public function connect () {

    	require ROOT . "/config.php";

        try {

            self::$db = new PDO('mysql:host=' . self::$config['database']['hostname'] . ';dbname=' . self::$config['database']['dbname'],
                                self::$config['database']['username'], 
                                self::$config['database']['password']);

            self::$db->query('SET NAMES utf8');
            self::$db->query('SET CHARACTER_SET utf8_unicode_ci');
            
            // TODO: Remove for production
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {

            echo 'No connection DB: ' . $e->getMessage();

        }

    }


}

?>