<?php
// api/companies.php 

require_once '../includes/db.inc.php'; // connects to database via $pdo

header('Content-Type: application/json'); // JSON output
header('Access-Control-Allow-Origin: *'); // allow API requests from any domain

if (isset($_GET['ref']) && $_GET['ref'] !== '') { // single company by symbol
    $stmt = $pdo->prepare("SELECT symbol, name, exchange, sector, description FROM companies WHERE symbol = ?"); 
    $stmt->execute([$_GET['ref']]); // run safely
    $row = $stmt->fetch(PDO::FETCH_ASSOC); // fetch single row
    echo json_encode($row ?: ["error" => "Company not found"]); // return one or error
    exit; // stop here
}

$stmt = $pdo->query("SELECT symbol, name, sector FROM companies ORDER BY symbol"); // all companies
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC); // fetch all
echo json_encode($rows); // output list
?>
