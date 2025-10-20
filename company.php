<?php
// this page shows details for one company + its history

require_once 'includes/db.inc.php'; 

// 1) get the symbol from the URL like company.php?symbol=AAPL
$symbol = '';
if (isset($_GET['symbol'])) {
  $symbol = $_GET['symbol']; 
}
if ($symbol === '') {
  // if no symbol was provided, stop and tell the user how to try again
  die('No company symbol provided. Try something like ?symbol=AAPL');
}

// 2) load the company’s basic info from the companies table
$sql = "SELECT symbol, name, exchange, sector, description
        FROM companies
        WHERE symbol = '" . $symbol . "'";
$st = $pdo->query($sql);                  
$company = $st->fetch(PDO::FETCH_ASSOC);  // get one row as an associative array

if (!$company) {
  // if the symbol doesn’t exist in the table, stop here
  die('Company not found.');
}

// 3) load the stock history for Jan–Mar 2019 (oldest first)
//    we only get this one company’s rows using WHERE symbol = ...
$hsql = "SELECT date, open, high, low, close, volume
         FROM history
         WHERE symbol = '" . $symbol . "'
           AND date >= '2019-01-01' AND date <= '2019-03-31'
         ORDER BY date ASC";
$hst = $pdo->query($hsql);                       // run the query
$hist = $hst->fetchAll(PDO::FETCH_ASSOC);        // get all rows (array of arrays)
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo $company['symbol']; ?> - Company</title>
  <link rel="stylesheet" href="assets/styles.css"> <!-- use the site styles -->
</head>
<body>
<div class="container">
  <!-- simple top nav to move around the site -->
  <div class="nav">
    <a href="index.php">Home</a>
    <a href="companies.php">Companies</a>
    <a href="about.php">About</a>
    <a href="api_tester.php">API Tester</a>
  </div>

  <!-- company info card -->
  <div class="card">
    <h1><?php echo $company['name']; ?> (<?php echo $company['symbol']; ?>)</h1>
    <p><strong>Exchange:</strong> <?php echo $company['exchange']; ?></p>
    <p><strong>Sector:</strong> <?php echo $company['sector']; ?></p>
    <p><strong>Description:</strong> <?php echo $company['description']; ?></p>
  </div>

  <!-- history table (Jan–Mar 2019) -->
  <div class="card">
    <h2>Daily History (Jan–Mar 2019)</h2>
    <table>
      <tr>
        <th>Date</th>
        <th>Open</th>
        <th>High</th>
        <th>Low</th>
        <th>Close</th>
        <th>Volume</th>
      </tr>
      <?php
      // loop through each history row and print it as a table row
      for ($i = 0; $i < count($hist); $i++) { ?>
        <tr>
          <td><?php echo $hist[$i]['date']; ?></td>
          <td><?php echo $hist[$i]['open']; ?></td>
          <td><?php echo $hist[$i]['high']; ?></td>
          <td><?php echo $hist[$i]['low']; ?></td>
          <td><?php echo $hist[$i]['close']; ?></td>
          <td><?php echo $hist[$i]['volume']; ?></td>
        </tr>
      <?php } ?>
    </table>
  </div>

  <!-- small footer link back to companies list -->
  <div class="footer">Back to <a href="companies.php">Companies</a></div>
</div>
</body>
</html>
