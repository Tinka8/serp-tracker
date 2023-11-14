<?php

// Údaje pro připojení k databázi
$servername = 'localhost';
$username = 'root';
$password = 'matejvolesini';
$dbname = 'serp_tracker';

// Vytvoření spojení
$conn = new mysqli($servername, $username, $password, $dbname);

// Kontrola spojení
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
