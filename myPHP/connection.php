<?php

$host = "localhost";
$user = "root";
$password = "super3";
$dbname = "trivial";
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
} catch (Exception $e) {
    print_r($e->getMessage());
}