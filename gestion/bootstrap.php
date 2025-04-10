<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

define ("DBHOST", $_ENV['DBHOST']);
define ("DBNAME", $_ENV['DBNAME']);
define ("DBUSER", $_ENV['DBUSER']);
define ("DBPASS", $_ENV['DBPASS']);
define ("DBPORT", $_ENV['DBPORT']);
define ("KEY" , $_ENV['KEY']);
// define ("DB_KEY", $_ENV['DB_KEY']);