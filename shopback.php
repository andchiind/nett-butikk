<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Receipt</title>
</head>

<body>

<h1>Digital Receipt</h1>

<p>
  <script src="shopfront.js"></script>

<?php

function updateStock() {
  define("STOCK_FILE_NAME", "stock.txt");
  define("STOCK_FILE_LINE_SIZE", 256);

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
      "photo" => $row[0],
      "name" => $row[1],
      "info" => $row[2],
      "price" => $row[3],
      "stock" => $stock); // Add new stock
    //$stock_list .= implode(",", $stock_item)."\n";
    //$stock_list[$row[0]] = $stock_item;
    //$stock_list[length] = $row;
    array_push($stock_list, $stock_item);
    //echo print_r($row);
    //fputcsv($f, $row);
    //echo implode(",", $stock_item)."<br />";
  }

  fclose($f);

  $f = fopen(STOCK_FILE_NAME, "w");

// crawdad,Crawdad,It's actually a 'crayfish'.,4.50,10
// gorilla,Gorilla,Gives a friendly wave.,8.50,11
// ninja,Ninja,Hero in a half-shell.,12.50,12
// psion,Psion 5,A computing classic - rare.,125.00,13
// totem,Totem,Mysterious and wooden (untold supernatural powers).,150.00,14

  //file_put_contents(STOCK_FILE_NAME, "");

  foreach ($stock_list as $line) {

    if ($line != null) {
      fputcsv($f, $line);
      //fwrite($f, implode(",", $line)."\n");
    }
  }

  //fwrite($f, $stock_list);

  fclose($f);
}

function getStock($item) {
  global $newStock;
  foreach (array_keys($newStock) as $item_name) {
    if ($item_name == $item) {
      return $newStock[$item_name];
    }
  }
  return false;
}

function getFormInfo($k) {
  return isset($_POST[$k]) ? htmlspecialchars($_POST[$k]) : null;
}

function wrongInfo($error) { // This standard for errors makes the program easy to expand
  global $correct_values;
  echo "The given information is not correct. <br />";
  echo $error."<br /><br />";
  $correct_values = false;
  echo "<form name=\"order\" action=\"shopfront.php\" method=\"POST\"> <input type=\"submit\" value=\"Return to store\" /> </form>";
}

function testCardNumber($v) {
  global $card_type;
  $first_char = substr($v, 0, 1);
  if ($card_type == "visa" AND $first_char != "4") {
    wrongInfo("A VISA card number should start with 4.");
  } elseif ($card_type == "master" AND $first_char != "5") {
    wrongInfo("A MASTERCARD card number should start with 5.");
  }
}

function testSecurityCode($v) {
  if (strlen($v) != 3 OR $v < 0) {
    wrongInfo("A security code should be three digits and positive.");
  }
}

function testItemQuantity($v) {
  if ($v < 1 OR strpos($v, ".") !== true) { //Check use of double ==. !!!!!!!!!!!
    wrongInfo("The quantity of items selected should be a positive integer.");
  }
}

function formatNames($name) {

  switch ($name) {
    case "sub_total":
        return "Sub Total";
    case "delivery_charge":
      return "Delivery Charge";
    case "vat":
      return "VAT";
    case "total":
      return "Total Cost";
    case "cc_type":
      return "Card Type";
    case "cc_number":
      return "Card Number";
    case "cc_name":
      return "Name on the Card";
    case "delivery_address":
      return "Delivery Address";
    case "email":
      return "Contact E-mail";
    default:
      return $name;
  }
}

$printout = "";
$correct_values = true;
$item_quantity = 0;
$item = true;
$card_type = "no card type specified";
$newStock = [];
foreach (array_keys($_POST) as $k) {
  global $newStock;
  global $card_type;
  global $item_quantity;
  global $correct_values;
  global $item;
  $display = true;

  if ($correct_values) {
    $v = getFormInfo($k);
    if ($v == "" OR $v == NULL) { //THIS MIGHT NOT BE USEFUL, SEE ITEM QUANTITY !!!!!!!!!!
      wrongInfo("Missing value for ".$k);
      break;
    }
    switch ($k) {
      case "cc_number":
        testCardNumber($v);
        $beginning = substr($v, 0, 2);
        $ending = substr($v, strlen($v) - 2, strlen($v) - 1);
        $v = $beginning."************".$ending; //Hides parts of card number
        break;
      case "cc_code":
        testSecurityCode($v);
        //$correct_values = false;
        $display = false;
        break;
      case "cc_type":
        $item = false;
        if ($item_quantity < 1) {
          wrongInfo("No items have been selected.");
        }
        if ($v == "visa") {
          $card_type = "visa";
        } else {
          $card_type = "master";
        }
        break;
      case "item_quantity":
        testItemQuantity($v);
        break;
      case "delivery_postcode": // This information is not displayed
        //$correct_values = false;
        $display = false;
        break;
      case "delivery_country": // This information is not displayed
        //$correct_values = false;
        $display = false;
        break;
      default:
        break;
    }
    if ($item) {
      $item_quantity += $v;
    }
    if ($display AND $correct_values AND $v != "" AND $v != NULL AND $v != "0") { //Treat numbers as Strings
      if (substr($k, -strlen("_line_cost")) == "_line_cost") { // Returns false if $k does not contain "line_cost"
        //https://www.codemiles.com/php-examples/check-if-string-ends-with-specific-sub-string-in-php-t10704.html
        $k = "Item Cost:";
        $v = $v." <br />";
      }
      if (substr($k, -strlen("_item_stock")) == "_item_stock") { // Returns false if $k does not contain "line_cost"
        $k = str_replace("_item_stock", "", $k);
        $newStock[$k] = $v;
        continue;
      }
      $k = formatNames($k);
      if ($k == "total") {
        $v .= "<br />";
      }
      $printout = $printout."{$k} : {$v}<br />\n";
    }
  }
}

if ($correct_values) {
  $date = getdate();
  $transaction_ID = strtoupper(uniqid());
  echo "Transaction ID: ".$transaction_ID."<br />";
  echo "Date of transaction: ";
  echo $date["mday"].".".$date["mon"].".".$date["year"].".<br /><br />";
  echo $printout."<br />";

  echo "<form name=\"order\" action=\"shopfront.php\" method=\"POST\"> <input type=\"submit\" value=\"Return to store\" /> </form>";

  updateStock();
}

?>

</p>

</body>
</html>
