<?php 

$host = 'localhost';
$db = 'db_guestbook';
$users = 'root';
$pass = '';
$charset = 'utf8';

$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$pdo = new PDO($dsn, $users, $pass, $options);

// var_dump($pdo);