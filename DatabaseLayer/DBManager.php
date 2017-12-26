<?php
define('ENVIRONMENT', 'production');

date_default_timezone_set('Asia/Kolkata');
/*
*
* Author : Mahantesh - A - Policepatil.
* Class : DBManager Class,
* This class is used for making a database connection,
* This class returns a connection object.
*
*/
class DBManager {
	
	/*
	*
	* Author : Mahantesh - A - Policepatil.
	* Database connection parameters.
	* The following parameters are used for making a database connection,
	*
	*/
	
	private $host     = 'localhost';
	private $dbname   = 'your db name';
	private $username = 'your db user name';
	private $password = 'your db password';
	
	
	public $dbconn = '';
	
	/*
	*
	* Author : Mahantesh - A - Policepatil.
	* Method : Constructor.
	*
	*/
	
	function __construct() {
	
		$this->connect();
	
	}
	
	function connect() {
	
		try {
						
			if (defined('ENVIRONMENT'))
			{
				switch (ENVIRONMENT)
				{
					case 'development':
						error_reporting(E_ALL);
						break;
			
					case 'testing':
					case 'production':
						error_reporting(0);
						break;
			
					default:
						exit('The application environment is not set correctly.');
				}
			}
			$this->dbconn = new PDO("mysql:host=$this->host;port=3306;dbname=$this->dbname",$this->username, $this->password);
			$this->dbconn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);	
	
		} catch(PDOException $e){
	
			echo 'We\'re sorry but there was an error while trying to connect to the database';
			file_put_contents('connection.errors.txt', $e->getMessage().PHP_EOL,FILE_APPEND);
	
		}
	}
	
	
}