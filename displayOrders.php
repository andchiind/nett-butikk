<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Orders</title>
</head>

<body>

<h1>Previous Orders</h1>

  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="shopfront.css" type="text/css" />
  </head>

  <body>

  <hr />

<stock_list class="list">


<?php

define("ORDERS_FILE_NAME", "orders.txt");
define("ORDERS_FILE_LINE_SIZE", 256);

/*
* This function prints out and displays the values in the orders.txt file.
*/
function printOrders() {
  clearstatcache();
  if (!file_exists(ORDERS_FILE_NAME)) {
    die("File not found for read - " . ORDERS_FILE_NAME . "\n");
  }
  $f = fopen(ORDERS_FILE_NAME, "r");
  $stock_list = null;

  while (($row = fgetcsv($f, ORDERS_FILE_LINE_SIZE)) != false) {
    $stock_item = array(
      "id" => $row[0],
      "date" => $row[1]);

    for ($i = 2; $i < sizeof($row); $i++) {
      $stock_item[$i - 2] = $row[$i];
    }

    $stock_list[$row[0]] = $stock_item;
  }

  fclose($f);

  foreach(array_keys($stock_list) as $id) {
    $item = $stock_list[$id];

    echo "  <stock_item class=\"item_row\" id=\"{$id}\">\n";
    echo "  <transaction_date>{$item["date"]} &nbsp;</transaction_date>\n";
    echo "  <transaction_id>{$item["id"]} &nbsp;</transaction_id>\n";

    //Because we do not know the amount of different items bought, we use a for loop
    for ($i = 0; $i < sizeof($item) - 2; $i++) {
      if ($i != sizeof($item) - 3) { //This only adds a comma if it is not the last item
        echo "  <item_{$i}>{$item[$i]},</item_{$i}>\n";
      } else {
        echo "  <item_{$i}>{$item[$i]}</item_{$i}>\n";
      }
    }
    echo "  </stock_item>\n";
  }
}

printOrders();

?>

</stock_list>

</body>
</html>
