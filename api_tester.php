<?php
$exampleSymbol = 'AAPL';
$exampleUserId = '1';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>API Tester</title>
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
    <h1>API Tester</h1>
    <h3>Companies API</h3>
    <p><a href="api/companies.php">All companies</a></p>
    <p><a href="api/companies.php?ref=<?php echo $exampleSymbol; ?>">Single company (<?php echo $exampleSymbol; ?>)</a></p>
    <form method="get" action="api/companies.php" class="input-row">
      <input type="text" name="ref" placeholder="Symbol (e.g., AAPL)">
      <button type="submit">Get Company</button>
    </form>
  </div>

  <div class="card">
    <h3>History API</h3>
    <p><a href="api/history.php?ref=<?php echo $exampleSymbol; ?>">History for <?php echo $exampleSymbol; ?> (Jan–Mar 2019)</a></p>
    <form method="get" action="api/history.php" class="input-row">
      <input type="text" name="ref" placeholder="Symbol (e.g., MSFT)">
      <button type="submit">Get History</button>
    </form>
  </div>

  <div class="card">
    <h3>Portfolio API</h3>
    <p><a href="api/portfolio.php?ref=<?php echo $exampleUserId; ?>">Portfolio for user <?php echo $exampleUserId; ?></a></p>
    <form method="get" action="api/portfolio.php" class="input-row">
      <input type="text" name="ref" placeholder="User ID (e.g., 1)">
      <button type="submit">Get Portfolio</button>
    </form>
  </div>


</div>
</body>
</html>
