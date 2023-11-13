<?php

// Údaje pro připojení k databázi
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "serp_tracker";

// Vytvoření spojení
$conn = new mysqli($servername, $username, $password, $dbname);

// Kontrola spojení
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Dotaz na databázi
$sql = "SELECT search_for_domain, search_for_phrase, position, `current_time` FROM serp_results";
