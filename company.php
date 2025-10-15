<?php
require_once 'includes/db.inc.php';

$symbol = isset($_GET['symbol']) ? $_GET['symbol'] : '';

if ($symbol == '') {
    die('No company symbol provided. Try something like ?symbol=AAPL');
}

// Get company info by symbol and stops if the symbol is not found.
$sql = "SELECT symbol, name, exchange, sector, description FROM companies WHERE symbol = '" . $symbol . "'";
$company = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    die('Company not found.');
}

// History Jan–Mar 2019: fetch date, open, high, low, close, volume for $symbol ordered by ascending date.
$hsql = "SELECT date, open, high, low, close, volume
         FROM history
         WHERE symbol = '" . $symbol . "'
           AND date >= '2019-01-01' AND date <= '2019-03-31'
         ORDER BY date ASC";
$hist = $pdo->query($hsql);  
?>


<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo $company['symbol']; ?> • Company</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<div class="container">
  <div class="nav">
  <a href="index.php">Home</a>
  <a href="about.php">About</a>
  <a href="api_tester.php">API Tester</a>
</div>

  <div class="card">
    <h1><?php echo $company['name']; ?> (<?php echo $company['symbol']; ?>)</h1>
    <p><strong>Exchange:</strong> <?php echo $company['exchange']; ?></p>
    <p><strong>Sector:</strong> <?php echo $company['sector']; ?></p>
    <p><strong>Description:</strong> <?php echo $company['description']; ?></p>
  </div>

  <div class="card">
    <h2>Daily History (Jan–Mar 2019)</h2>
    <table>
      <tr>
        <th>Date</th><th>Open</th><th>High</th><th>Low</th><th>Close</th><th>Volume</th>
      </tr>
      <?php foreach($hist as $h) { ?>
        <tr>
          <td><?php echo $h['date']; ?></td>
          <td><?php echo $h['open']; ?></td>
          <td><?php echo $h['high']; ?></td>
          <td><?php echo $h['low']; ?></td>
          <td><?php echo $h['close']; ?></td>
          <td><?php echo $h['volume']; ?></td>
        </tr>
      <?php } ?>
    </table>
  </div>

  <div class="footer">Back to <a href="index.php">Companies</a></div>
</div>
</body>
</html>
