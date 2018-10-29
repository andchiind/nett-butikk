<form id="form" action="updateStock.php" method="post">
<?php

  function updateStock() {
    global $newStock;
    define("STOCK_FILE_NAME", "stock.txt");
    define("STOCK_FILE_LINE_SIZE", 256);

    if (!file_exists(STOCK_FILE_NAME)) {
      die("File not found for read - " . STOCK_FILE_NAME . "\n");
    }
    $f = fopen(STOCK_FILE_NAME, "r");
    $stock_list = [];
    while (($row = fgetcsv($f, STOCK_FILE_LINE_SIZE)) != false) {
      $stock = $newStock[$row[0]];
      $stock_item = array(
        "id" => $row[0], // The photo is ignored since it is identical to "id"
        "name" => $row[1],
        "info" => $row[2],
        "price" => $row[3],
        "stock" => $stock); // Refresh stock
      array_push($stock_list, $stock_item);
    }

    fclose($f);
    $f = fopen(STOCK_FILE_NAME, "w");

    foreach ($stock_list as $line) {
      if ($line != null) {
        fputcsv($f, $line);
      }
    }
    fclose($f);
  }

  function getFormInfo($k) {
    return isset($_POST[$k]) ? htmlspecialchars($_POST[$k]) : null;
  }

  $newStock = [];
  foreach (array_keys($_POST) as $k) {
    $v = getFormInfo($k);
    $newStock[$k] = $v;
  }

  updateStock();

  foreach ($_POST as $a => $b) {
      echo '<input type="hidden" name="'.htmlentities($a).'" value="'.htmlentities($b).'">';
  }

?>
</form>
<script type="text/javascript">
    document.getElementById('form').submit();
</script>
