<?php
// Include the database connection file.
require_once 'includes/db.inc.php';

// SQL query to fetch all companies (symbol, name, exchange, sector) ordered by symbol.
$sql = "SELECT symbol, name, exchange, sector FROM companies ORDER BY symbol";

// Run the query (safe to use ->query() since no user input).
$st = $pdo->query($sql);
// Fetch all rows as associative arrays (keys: symbol, name, exchange, sector).
$rows = $st->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Company Info</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<div class="container">
  <div class="nav">
    <a href="index.php">Home</a>
    <a href="companies.php">Company's</a>
    <a href="about.php">About</a>
    <a href="api_tester.php">API Tester</a>
  </div>

  <?php 
  // Card container for the companies section ?>
  <div class="card">
    <h1>Company History</h1>
    <table>
      <tr>
        <th>Symbol</th><th>Name</th><th>Exchange</th><th>Sector</th>
      </tr>
      <?php foreach ($rows as $r) { ?>
        <?php
        // Show the company symbol as a clickable link to the details page).
        // Navigates to the per-company view using the symbol as a parameter.
        ?>
        <tr>
          <td><a href="company_details.php?symbol=<?php echo $r['symbol']; ?>"><?php echo $r['symbol']; ?></a></td>
          <?php // Show the company name as a link to the same details page. ?>
          <td><a href="company_details.php?symbol=<?php echo $r['symbol']; ?>"><?php echo $r['name']; ?></a></td>
           <?php // Display the exchange for this company (e.g., NASDAQ, NYSE) ?>
          <td><?php echo $r['exchange']; ?></td>
          <?php // Display the sector for this company (e.g., Technology, Healthcare) ?>
          <td><?php echo $r['sector']; ?></td>
        </tr>
      <?php } ?>
    </table>
  </div>
</div>
</body>
</html>
