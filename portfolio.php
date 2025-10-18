<?php

require_once 'includes/db.inc.php';

/* 1) Fetch the list of users from the 'users' table.
   - This query retrieves the 'id' column for all users, ordered by their 'id'. 
   - We're only displaying "User #id" on the page, so the actual user information is not necessary here. */
$sql = "
  SELECT id
  FROM users
  ORDER BY id
";
// Execute the SQL query and fetch the results
$st = $pdo->query($sql);
// Store the result in the $users array as an associative array
$users = $st->fetchAll(PDO::FETCH_ASSOC);

/* 2) Get the selected user ID from the URL parameter (?userId=).
   - If the user ID is not provided, default to 0 (meaning no user is selected). */
$userId = isset($_GET['userId']) ? (int)$_GET['userId'] : 0;

/* 3) Initialize variables for calculating portfolio summary:
   - $rows will hold the portfolio rows (stock holdings) for the selected user.
   - $companies, $shares, and $total will hold the summary statistics for the portfolio. */
$rows = array();
$companies = 0; // Track the number of different companies in the portfolio
$shares = 0;    // Track the total number of shares in the portfolio
$total = 0.0;   // Track the total value of the portfolio in dollars

// If a user is selected (i.e., userId > 0), fetch the portfolio data for that user
if ($userId > 0) {
  // SQL query to fetch portfolio data for the selected user
  // - Joins the 'portfolio' table (holds user stock amounts) with the 'companies' table (stock info like symbol, name, and sector)
  // - Also joins with the 'history' table to fetch the latest stock closing price.
  // - The subquery in the WHERE clause ensures we get the most recent close price for each symbol.
  $sqlRows = "
    SELECT
      c.symbol,        
      c.name,         
      c.sector,        
      p.amount,        
      h.close AS last_close  -- The most recent closing price for the stock
    FROM portfolio p
    JOIN companies c ON p.symbol = c.symbol       
    JOIN history h ON h.symbol = c.symbol          
                    AND h.date = (SELECT MAX(h2.date) FROM history h2 WHERE h2.symbol = c.symbol)  
    WHERE p.userId = $userId                        
    ORDER BY c.symbol                               
  ";
  // Execute the SQL query and store the portfolio data in the $rows array
  $st = $pdo->query($sqlRows);
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);

  // Loop through each portfolio row to calculate summary values
  foreach ($rows as $r) {
    $companies += 1;  // Increment the number of companies in the portfolio
    $shares += (int)$r['amount'];  // Add the amount of shares for this symbol to the total share count
    $total += ((float)$r['last_close']) * ((int)$r['amount']);  // Add the value of this stock holding to the total portfolio value
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Portfolio</title>
  <!-- Link to external stylesheet for the page's styling -->
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<div class="container">
  <div class="nav">
    <!-- Navigation links for the main sections of the site -->
    <a href="index.php">Home</a>
    <a href="portfolio.php">Portfolio</a>
    <a href="about.php">About</a>
    <a href="api_tester.php">API Tester</a>
  </div>

  <!-- Section to display the list of customers (users) -->
  <div class="card">
    <h1>Customers</h1>
    <table>
      <tr><th>User</th><th>Action</th></tr>
      <!-- Loop through the users array and display each user with a link to their portfolio -->
      <?php foreach ($users as $u) { ?>
        <tr>
          <!-- Display user ID as "User #id" -->
          <td><?php echo 'User #'.$u['id']; ?></td>
          <!-- Create a link to the portfolio page for this user, passing the userId in the URL -->
          <td><a href="portfolio.php?userId=<?php echo $u['id']; ?>">Portfolio</a></td>
        </tr>
      <?php } ?>
    </table>
  </div>

  <!-- Portfolio summary section (only displayed when a user is selected) -->
  <div class="card">
    <h1>Portfolio Summary</h1>
    <?php if ($userId === 0) { ?>
      <!-- If no user is selected, prompt the user to select one -->
      <p>Select a customer on the left to view their portfolio.</p>
    <?php } else { ?>
      <!-- If a user is selected, display their portfolio summary -->
      <div class="summary-grid">
        <div><strong>Companies</strong><div><?php echo $companies; ?></div></div>
        <div><strong># Shares</strong><div><?php echo $shares; ?></div></div>
        <div><strong>Total Value</strong><div>$<?php echo number_format($total, 2); ?></div></div>
      </div>
    <?php } ?>
  </div>

  <!-- Portfolio details section (display individual stock holdings for the selected user) -->
  <div class="card">
    <h2>Portfolio Details</h2>
    <?php if ($userId === 0) { ?>
      <!-- If no user is selected, prompt the user to select one -->
      <p>Select a customer above.</p>
    <?php } else if (count($rows) === 0) { ?>
      <!-- If the selected user has no portfolio, inform the user -->
      <p>No portfolio rows found for this customer.</p>
    <?php } else { ?>
      <!-- If the user has a portfolio, display a table with their stock holdings -->
      <table>
        <tr>
          <th>Symbol</th>
          <th>Name</th>
          <th>Sector</th>
          <th>Amount</th>
          <th>Last Close</th>
          <th>Value</th>
        </tr>
        <?php foreach ($rows as $r) {
          // Calculate the value of the current stock holding (last close price * amount of shares)
          $val = ((float)$r['last_close']) * ((int)$r['amount']);
        ?>
          <tr>
            <!-- Link to the company page using the stock symbol -->
            <td><a href="company.php?symbol=<?php echo $r['symbol']; ?>"><?php echo $r['symbol']; ?></a></td>
            <td><a href="company.php?symbol=<?php echo $r['symbol']; ?>"><?php echo $r['name']; ?></a></td>
            <td><?php echo $r['sector']; ?></td>
            <td><?php echo (int)$r['amount']; ?></td>
            <td>$<?php echo number_format((float)$r['last_close'], 2); ?></td>
            <td>$<?php echo number_format($val, 2); ?></td>
          </tr>
        <?php } ?>
      </table>
    <?php } ?>
  </div>

  <!-- Footer with a link back to the companies page -->
  <div class="footer">Back to <a href="index.php">Companies</a></div>
</div>
</body>
</html>
