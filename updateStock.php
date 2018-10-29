<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Stock Updates</title>
</head>

<body>

<h1>Update Stock</h1>

<p>

  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="shopfront.css" type="text/css" />
    <title>Items for sale</title>
  </head>

  <body>

  <h2>Items for Sale</h2>

  <hr />

  <!--CHANGE VARIABLE NAMES!!!!!!!!!!!!-->

  <stock_item>
    <item_name class="heading">Name</item_name>
    <item_info class="heading">Description</item_info>
    <item_quantity class="heading">New Stock</item_quantity>
    <item_stock class="heading">Current Stock</item_stock>
  </stock_item>

  </p>

<?php

$newStock = [];

define("STOCK_FILE_NAME", "stock.txt");
define("STOCK_FILE_LINE_SIZE", 256);
function updateStock() {
  if (!file_exists(STOCK_FILE_NAME)) {
    die("File not found for read - " . STOCK_FILE_NAME . "\n");
  }
  $f = fopen(STOCK_FILE_NAME, "r");
  $stock_list = [];
  while (($row = fgetcsv($f, STOCK_FILE_LINE_SIZE)) != false) {
    $stock = $row[4];
    if (getStock($row[0]) != false) { //Check if the stock has changed
      $stock = getStock($row[0]);
    }
    $stock_item = array(
      "id" => $row[0],
      "name" => $row[1],
      "info" => $row[2],
      "price" => $row[3],
      "stock" => $stock);
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
  printStock();
}

function getStock($item) {

}

function printStock() {
  clearstatcache(); // http://php.net/manual/en/function.clearstatcache.php
  if (!file_exists(STOCK_FILE_NAME)) {
    die("File not found for read - " . STOCK_FILE_NAME . "\n"); // Script exits.
  }
  $f = fopen(STOCK_FILE_NAME, "r");
  $stock_list = null;
  print_r($stock_list);
  while (($row = fgetcsv($f, STOCK_FILE_LINE_SIZE)) != false) {
    $stock_item = array(
      "id" => $row[0],
      "name" => $row[1],
      "info" => $row[2],
      "price" => $row[3],
      "stock" => $row[4]);
    $stock_list[$row[0]] = $stock_item; // Add stock.
  }
  fclose($f);
  foreach(array_keys($stock_list) as $id) {
    echo "  <stock_item id=\"{$id}\">\n";
    $item = $stock_list[$id];
    echo "    <item_name>{$item["name"]}</item_name>\n";
    echo "    <item_info>{$item["info"]}</item_info>\n";
    echo "    <new_stock value=\"{$item["stock"]}\"><input name=\"{$id}\" onblur=\"unSelectInput(this, {$item["stock"]});\" type=\"text\" value=\"{$item["stock"]}\" pattern=\"[0-9]+\" size=\"3\" onchange=\"updateStock();\" /></new+stock>\n";
    echo "    <item_stock class=\"stock\">{$item["stock"]}</item_stock>\n"; //This value is changed and displayed
    echo "      <total_stock style=\"display:none\">{$item["stock"]}</item_stock>\n"; //This stores the total stock
    echo "  </stock_item>\n\n";
  }
}
printStock();
?>

<script>

function unSelectInput(input_box, stock) {
  if (input_box.value == "" || input_box.value < 0) {
    input_box.value = stock;
  }
}

function updateStock() {
  
}
</script>

</body>
</html>
