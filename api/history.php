<?php
// /api/history.php
// used the comp 3512 course labs for help.

require_once '../includes/db.inc.php'; // connects to database via $pdo

header('Content-Type: application/json'); // JSON output
header('Access-Control-Allow-Origin: *'); // allow API requests from any domain

if (!isset($_GET['ref']) || $_GET['ref'] === '') {  // check if ?ref query is missing or empty
    echo json_encode(["error" => "Missing ref (symbol)"]); 
    exit;
}

// prepare SQL to get stock history for given company symbol and date range
$stmt = $pdo->prepare("
    SELECT date, open, high, low, close, volume
    FROM history
    WHERE symbol = ?
      AND date >= ? AND date <= ?
    ORDER BY date ASC
");

$stmt->execute([$_GET['ref'], '2019-01-01', '2019-03-31']); // execute safely with parameters
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);  // fetch all matching rows as associative array
echo json_encode($rows);  // output result as JSON
?>