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

// Spustit dotaz na databázi
$result = $conn->query($sql);

// Připravit prázdné pole pro ukládání výsledků
$data = [];

// Zjistíme, jestli máme více než 0 výsledků
if ($result->num_rows > 0) {
    // Procházení řádků
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
	// Pokud nebyly nalezeny žádné výsledky
    echo json_encode(['error' => 'No records found']);

	// Ukončit připojení k databázi
    $conn->close();

	// Ukončit skript
    exit();
}

// Nastavíme hlavičku odpovědi Content-type
header('Content-Type: application/json');

// Vypsat JSON formátované pole $data
echo json_encode($data);

// Uzavření spojení
$conn->close();