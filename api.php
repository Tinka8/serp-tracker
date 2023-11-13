<?php

// Spustit dotaz na databázi
$result = $conn->query($sql);

// Připravit prázdné pole pro ukládání výsledků
$data = [];

// Zjistíme, jestli máme více než 0 výsledků
if ($result->num_rows > 0) {
    // Procházení řádků
    while ($row = $result->fetch_assoc()) {
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

// Uzavření spojení
$conn->close();