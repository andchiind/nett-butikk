<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Item Updates</title>
</head>

<body>

<h1>Update Item details</h1>

<p>

  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="shopfront.css" type="text/css" />
    <title>Items for sale</title>
  </head>

  <body>

  <h2>Items for Sale</h2>

  <hr />

  <form id="form" action="stockRedirect.php" method="post">

  <stock_list>

  <stock_item>
    <item_name class="heading">Name</item_name>
    <item_info class="heading">Description</item_info>
    <item_price class="heading">Price in pounds</item_price>
    <new_stock class="heading">New Stock</new_stock>
    <item_stock class="heading">Current Stock</item_stock>
  </stock_item>

  </p>

<?php

$newStock = [];

define("STOCK_FILE_NAME", "stock.txt");
define("STOCK_FILE_LINE_SIZE", 256);

function printStock() {
  clearstatcache();
  if (!file_exists(STOCK_FILE_NAME)) {
    die("File not found for read - " . STOCK_FILE_NAME . "\n");
  }
  $f = fopen(STOCK_FILE_NAME, "r");
  $stock_list = null;

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
    echo "    <item_name><input name=\"{$id}_new_name\" onblur=\"unSelectInput(this, {$item["name"]});\" type=\"text\" value=\"{$item["name"]}\" placeholder=\"{$item["name"]}\" size=\"20\" /></item_name>\n";
    echo "    <item_info><input name=\"{$id}_new_info\" onblur=\"unSelectInput(this, {$item["info"]});\" type=\"text\" value=\"{$item["info"]}\" placeholder=\"{$item["info"]}\" size=\"50\" /></item_info>\n";
    echo "    <item_price><input name=\"{$id}_new_price\" onblur=\"unSelectInput(this, {$item["price"]});\" type=\"text\" value=\"{$item["price"]}\" placeholder=\"{$item["price"]}\" pattern=\"^[0-9]\d*(\.\d+)?$\" size=\"5\" /></item_price>\n";
    echo "    <new_stock><input name=\"{$id}_new_stock\" onblur=\"unSelectInput(this, {$item["stock"]});\" type=\"text\" value=\"{$item["stock"]}\" placeholder=\"{$item["stock"]}\" pattern=\"[0-9]+\" size=\"3\" /></new_stock>\n";
    echo "    <item_stock class=\"stock\">{$item["stock"]}</item_stock>\n"; //This value is changed and displayed
    echo "      <total_stock style=\"display:none\">{$item["stock"]}</item_stock>\n"; //This stores the total stock
    // echo "    <input value=\"Delete\" type=\"button\" onclick=\"deleteItem({$id})/>\n";
    echo "  </stock_item>\n\n";
  }
}
printStock();
?>

</stock_list>

<br />

<input type="submit" value="Update Values" />

</form>

<script>

function unSelectInput(input_box, value) {
  if (input_box.value == "" || input_box.value < 0) {
    input_box.value = value;
  }
}

// function deleteItem(id) {
//   let item = document.getElementById(id);
//   item.style.display = "none";
//   item
// }

</script>

</body>
</html>
