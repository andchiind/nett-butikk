<form id="form" action="updateStock.php" method="post">
<?php

$correct_values = true;

if (empty($_POST)) {
  echo "<h1>Error</h1>";
  echo "<p>";
  echo "This page was not accessed correctly. <br />";
  echo "Your are probably looking for \"Item updates\".<br /><br />";
  echo "</p>";
  $correct_values = false;
  echo "<error id=\"error\"></error>";
  echo "<form id=\"order\" action=\"updateStock.php\" method=\"POST\"> <input type=\"submit\" value=\"Go to Item Updates\" /> </form>";
}

  /*
  * This function updates the stock.txt file with the values submitted in updateStock.php
  */
  function updateStock() {
    global $newStock;
    global $newName;
    global $newInfo;
    global $newPrice;
    define("STOCK_FILE_NAME", "stock.txt");
    define("STOCK_FILE_LINE_SIZE", 256);

    if (!file_exists(STOCK_FILE_NAME)) {
      die("File not found for read - " . STOCK_FILE_NAME . "\n");
    }

    $f = fopen(STOCK_FILE_NAME, "r");
    $stock_list = [];

    //In this loop the new values are placed inside an array of arrays.
    while (($row = fgetcsv($f, STOCK_FILE_LINE_SIZE)) != false) {
      $stock = $newStock[$row[0]];
      $name = $newName[$row[0]];
      $info = $newInfo[$row[0]];
      $price = $newPrice[$row[0]];
      $stock_item = array(
        "id" => $row[0], // The photo is ignored since it is identical to "id"
        "name" => $name,
        "info" => $info,
        "price" => $price,
        "stock" => $stock);
      array_push($stock_list, $stock_item);
    }

    fclose($f);
    $f = fopen(STOCK_FILE_NAME, "w"); //This deletes the content of the file

    //Here, each line in the new csv file is an element in the $stock_list array
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
  $newName = [];
  $newInfo = [];
  $newPrice = [];
  foreach (array_keys($_POST) as $k) {
    $v = getFormInfo($k);

    if (substr($k, -strlen("_new_stock")) == "_new_stock") {
      $k = str_replace("_new_stock", "", $k);
      $newStock[$k] = $v;

    } else if (substr($k, -strlen("_new_name")) == "_new_name") {
      $k = str_replace("_new_name", "", $k);
      $newName[$k] = $v;

    } else if (substr($k, -strlen("_new_info")) == "_new_info") {
      $k = str_replace("_new_info", "", $k);
      $newInfo[$k] = $v;

    } else if (substr($k, -strlen("_new_price")) == "_new_price") {
      $k = str_replace("_new_price", "", $k);
      $newPrice[$k] = $v;

    } else {
      echo "Unknown values, name: ".$k.", value: ".$v."<br />";
    }
  }

  if ($correct_values) {
    updateStock();
  }

  // The post values are reused in the new form which is submitted
  foreach ($_POST as $k => $v) {
      echo '<input type="hidden" name="'.htmlentities($k).'" value="'.htmlentities($v).'">';
  }

?>
</form>
<script type="text/javascript">
    // Once the stock.txt file has been updated, the page redirect to the updateStock.php page,
    // given that the POST values are not empty
    if (!document.contains(document.getElementById("error"))) {
      document.getElementById('form').submit();
    }
</script>
