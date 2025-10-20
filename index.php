<?php
// home page that shows customers and their portfolios

require_once 'includes/db.inc.php';

// 1) load users (id, firstname, lastname)
$sql = "SELECT id, firstname, lastname FROM users ORDER BY lastname, firstname";
$st = $pdo->query($sql);
$users = $st->fetchAll(PDO::FETCH_ASSOC);

// 2) pick which user to show (?userId=...)
$userId = 0;
if (isset($_GET['userId'])) {
  $userId = (int) $_GET['userId'];
}

// 3) if a user is selected, load their portfolio rows (no fancy SQL)
//    step A: get holdings with company info (no prices yet)
$rows = array();
$companies = 0;
$shares = 0;
$total = 0.0;

if ($userId > 0) {
  $sql = "
    SELECT
      c.symbol,
      c.name,
      c.sector,
      p.amount
    FROM portfolio p
    JOIN companies c ON p.symbol = c.symbol
    WHERE p.userId = " . $userId . "
    ORDER BY c.symbol
  ";
  $st = $pdo->query($sql);
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);

  // step B: for each holding, look up the latest close and calculate value
  for ($i = 0; $i < count($rows); $i++) {
    $sym = $rows[$i]['symbol'];

    // latest close for this symbol (take newest date)
    $hsql = "SELECT close FROM history WHERE symbol = '" . $sym . "' ORDER BY date DESC LIMIT 1";
    $hst  = $pdo->query($hsql);
    $hrow = $hst->fetch(PDO::FETCH_ASSOC);

    $lastClose = 0.0;
    if ($hrow) {
      $lastClose = (float) $hrow['close'];
    }

    $amt = (int) $rows[$i]['amount'];
    $val = $lastClose * $amt;

    // attach to row so we can print later
    $rows[$i]['last_close'] = $lastClose;
    $rows[$i]['value'] = $val;

    // summary numbers
    $companies = $companies + 1;
    $shares = $shares + $amt;
    $total = $total + $val;
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Home • Portfolio</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<div class="container">
  <div class="nav">
    <a href="index.php">Home</a>
    <a href="companies.php">Companies</a>
    <a href="about.php">About</a>
    <a href="api_tester.php">API Tester</a>
  </div>

  <!-- Customers -->
  <div class="card">
    <h1>Customers</h1>
    <table>
      <tr><th>Name</th><th>Action</th></tr>
      <?php
      
      for ($i = 0; $i < count($users); $i++) {
        $u = $users[$i];
        $label = $u['lastname'] . ', ' . $u['firstname'];
      ?>

        <tr>
          <td><?php echo $label; ?></td>
          <td><a href="index.php?userId=<?php echo (int) $u['id']; ?>">View Portfolio</a></td>
        </tr>
      <?php } ?>
    </table>
  </div>

  <!-- Portfolio Summary -->
  <div class="card">
    <h1>Portfolio Summary</h1>
    <?php if ($userId === 0) { ?>
      <p>Select a customer on the left to view their portfolio.</p>
    <?php } else { ?>
      <div class="summary-grid">
        <div><strong>Companies</strong><div><?php echo $companies; ?></div></div>
        <div><strong># Shares</strong><div><?php echo $shares; ?></div></div>
        <div><strong>Total Value</strong><div>$<?php echo number_format($total, 2); ?></div></div>
      </div>
    <?php } ?>
  </div>

  <!-- Portfolio Details -->
  <div class="card">
    <h2>Portfolio Details</h2>
    <?php if ($userId === 0) { ?>
      <p>Select a customer above.</p>
    <?php } else if (count($rows) === 0) { ?>
      <p>No portfolio rows found for this customer.</p>
    <?php } else { ?>
      <table>
        <tr>
          <th>Symbol</th>
          <th>Name</th>
          <th>Sector</th>
          <th>Amount</th>
          <th>Value</th>
        </tr>
        <?php for ($i = 0; $i < count($rows); $i++) {
          $r = $rows[$i];
          $valFormatted = number_format((float)$r['value'], 2);
        ?>
          <tr>
            <td><a href="company_details.php?symbol=<?php echo $r['symbol']; ?>"><?php echo $r['symbol']; ?></a></td>
            <td><a href="company_details.php?symbol=<?php echo $r['symbol']; ?>"><?php echo $r['name']; ?></a></td>
            <td><?php echo $r['sector']; ?></td>
            <td><?php echo (int) $r['amount']; ?></td>
            <td>$<?php echo $valFormatted; ?></td>
          </tr>
        <?php } ?>
      </table>
    <?php } ?>
  </div>
</div>
</body>
</html>
