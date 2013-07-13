<?php
class DBFactory
{
  public static function getMysqlConnexionWithPDO()
  {
    $db = new PDO('mysql:host=localhost;dbname=esec', 'login', 'pass');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     
    return $db;
  }
   
  public static function getMysqlConnexionWithMySQLi()
  {
    return new MySQLi('localhost', 'login', 'pass', 'dbname');
  }
}