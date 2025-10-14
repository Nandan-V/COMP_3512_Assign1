<?php
/* -----------------------------------------------------------
   index.php (data section)
   Load data for the sidebar list of companies and,
   if a symbol is chosen (?symbol=...), load that
   company's Q1 2019 history and simple summary stats.
----------------------------------------------------------- */

require_once 'includes/db.inc.php';   

/* -----------------------------
   1) Get companies for sidebar
   ----------------------------- */
$sqlList   = "SELECT symbol, name FROM companies ORDER BY symbol";
$companies = $pdo->query($sqlList);   // we’ll loop over this later in HTML

/* ------------------------------------------
   2) Read the chosen symbol from the address
      bar. Example: index.php?symbol=AAPL
   ------------------------------------------ */
$symbol = '';
if (isset($_GET['symbol'])) {
    $symbol = $_GET['symbol'];        
}

/* ----------------------------------------------------
   3) Prepare variables for the “main panel” on the
      right side. These start empty and only get filled
      if the user picked a symbol.
   ---------------------------------------------------- */
$company   = false;   // will hold one row from companies table
$history   = array(); // will hold many rows from history table
$histHigh  = 0;       // highest “high” price in Q1 2019
$histLow   = 0;       // lowest “low” price in Q1 2019
$totalVol  = 0;       // sum of “volume” over Q1 2019
$avgVol    = 0;       // average volume over Q1 2019

/* ----------------------------------------------------
   4) If a symbol is chosen, load:
      - the company info
      - the Q1 2019 daily history for that symbol
      - simple summary numbers (high, low, totals)
   ---------------------------------------------------- */
if ($symbol != '') {

    /* 4a) Company info (one row) */
    $sqlCompany = "
        SELECT symbol, name, exchange, sector, description
        FROM companies
        WHERE symbol = '" . $symbol . "'
    ";
    $company = $pdo->query($sqlCompany)->fetch(PDO::FETCH_ASSOC);

    /* Only continue if we actually found that company */
    if ($company) {

        /* 4b) Daily history for Q1 2019 (many rows) */
        $sqlHist = "
            SELECT date, open, high, low, close, volume
            FROM history
            WHERE symbol = '" . $symbol . "'
              AND date >= '2019-01-01' AND date <= '2019-03-31'
            ORDER BY date DESC
        ";
        $history = $pdo->query($sqlHist)->fetchAll(PDO::FETCH_ASSOC);

        /* 4c) Build the summary numbers in a beginner-friendly way
               - We’ll walk the rows once and update our totals. */
        $days = 0;       // how many rows (days) we saw
        $histHigh = null;  // start as “unknown”
        $histLow  = null;

        foreach ($history as $row) {
            // Pull out the numbers for this day
            $hi  = (float)$row['high'];
            $lo  = (float)$row['low'];
            $vol = (int)$row['volume'];

            // Set first values on the first loop
            if ($histHigh === null) { $histHigh = $hi; }
            if ($histLow  === null) { $histLow  = $lo; }

            // Update high/low if today beats our current record
            if ($hi > $histHigh) { $histHigh = $hi; }
            if ($lo < $histLow)  { $histLow  = $lo; }

            // Add to totals and count how many days we processed
            $totalVol += $vol;
            $days++;
        }

        // Average volume is total divided by number of days (if any)
        if ($days > 0) {
            $avgVol = (int) ($totalVol / $days);
        } else {
            $histHigh = 0;
            $histLow  = 0;
            $avgVol   = 0;
        }
    } // end if ($company)
} // end if ($symbol != '')

?>
