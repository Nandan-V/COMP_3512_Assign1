<?php
// Simple list for the "API List" table (URL + Description)
$apiSamples = [
  ['api/companies.php',         'Returns all the companies/stocks'],
  ['api/companies.php?ref=ADS', 'Return just a specific company/stock'],
  ['api/portfolio.php?ref=1',   'Returns all the portfolios for a specific sample customer'],
  ['api/history.php?ref=ADS',   'Returns the history information for a specific sample company']
];
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
    <a href="companies.php">Company's</a>
    <a href="about.php">About</a>
    <a href="api_tester.php">API Tester</a>
  </div>

  <!-- API List (sample hyperlinks + descriptions) -->
  <div class="card">
    <h1>API List</h1>
    <table>
      <tr>
        <th>URL</th>
        <th>Description</th>
      </tr>
      <?php for ($i = 0; $i < count($apiSamples); $i++) { ?>
        <tr>
          <td><a href="<?php echo $apiSamples[$i][0]; ?>"><?php echo '/' . $apiSamples[$i][0]; ?></a></td>
          <td><?php echo $apiSamples[$i][1]; ?></td>
        </tr>
      <?php } ?>
    </table>
    <p>Click any link above to see the JSON response.</p>
  </div>

  
</div>
</body>
</html>
