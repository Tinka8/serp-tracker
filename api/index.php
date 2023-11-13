<?php

$search_for_domain = $_GET["domain"] ?? null;
$search_for_phrase = $_GET["phrase"] ?? null;

include "../db.php";

$sql = "SELECT search_for_domain, search_for_phrase, min(position) as `position`, date(`current_time`) as `date` FROM serp_results";

if ($search_for_domain) {
  $sql .= " WHERE search_for_domain = '$search_for_domain'";
} elseif ($search_for_phrase) {
  $sql .= " WHERE search_for_phrase = '$search_for_phrase'";
}

$sql .= " GROUP BY search_for_domain, search_for_phrase, date(`current_time`) ORDER BY date(`current_time`) ASC";

$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
  // Procházení řádků
  while ($row = $result->fetch_assoc()) {
    $data[] = $row;
  }
} else {
  echo json_encode(["error" => "No records found"]);
  $conn->close();
  exit();
}

// Nastavíme hlavičku odpovědi Content-type
header("Content-Type: application/json");

// Uzavření spojení
$conn->close();

// Get unique dates from data
$labels = array_unique(array_column($data, "date"));

// Get max fond position
$max = max(array_column($data, "position"));

// Group by search_for_domain and search_for_phrase
$datasets = array_reduce($data, function ($carry, $item) {
    $carry[$item["search_for_domain"]][$item["search_for_phrase"]][$item['date']] = $item;
    return $carry;
}, []);

echo json_encode(compact('datasets', 'labels', 'max'));