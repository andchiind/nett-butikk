<!DOCTYPE html>
<html>

  <body>

    <h1>Error</h1>

  <form id="form" action="shopback.php" method="post">

<?php

/*
* Here, the stock.txt file is updated by decreasing the stock with
* the quantity of items bought.
*/
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
  $f = fopen(STOCK_FILE_NAME, "w"); // This empties the file

  foreach ($stock_list as $line) {
    if ($line != null) {
      fputcsv($f, $line);
    }
  }
  fclose($f);
}

/*
* The orders.txt file is updated with the new transaction.
*/
function updateRecord() {
  global $record;
  define("RECORD_FILE_NAME", "orders.txt");

  $f = RECORD_FILE_NAME;

  file_put_contents($f, $record, FILE_APPEND);
}

function getFormInfo($k) {
  return isset($_POST[$k]) ? htmlspecialchars($_POST[$k]) : null;
}

/*
* This standard for errors makes the program easy to expand.
*/
function wrongInfo($error) {
  global $correct_values;
  echo "<p>";
  echo "The given information is not correct. <br />";
  echo $error."<br /><br />";
  echo "</p>";
  $correct_values = false;
  echo "<error id=\"error\"></error>";
  echo "<form id=\"order\" action=\"shopfront.php\" method=\"POST\"> <input type=\"submit\" value=\"Return to store\" /> </form>";
}

/*
* Tests that mastercards start with 5, and visas start with 4
*/
function testCardNumber($v) {
  global $card_type;
  $first_char = substr($v, 0, 1);
  if ($card_type == "visa" AND $first_char != "4") {
    wrongInfo("A VISA card number should start with 4.");
  } elseif ($card_type == "master" AND $first_char != "5") {
    wrongInfo("A MASTERCARD card number should start with 5.");
  }
}

/*
* Tests that the security code is three digits and positive
*/
function testSecurityCode($v) {
  if (strlen($v) != 3 OR $v < 0) {
    wrongInfo("A security code should be three digits and positive.");
  }
}

/*
* Tests that there is a positive number of item
*/
function testItemQuantity($v) {
  if ($v < 1 OR strpos($v, ".") !== true) {
    wrongInfo("The quantity of items selected should be a positive integer.");
  }
}
/*
* This method is used in order to find line cost, total cost and email values.
* The switch statement removes the other possibilites when finding the line cost.
*/
function formatNames($name, $v) {
  global $record;
  global $total_cost_record;
  global $email_record;
  switch ($name) {
    case "sub_total":
        return;
    case "delivery_charge":
      return;
    case "vat":
      return;
    case "total":
    $total_cost_record .= "Total Cost: ".$v;
      return;
    case "cc_type":
      return;
    case "cc_number":
      return;
    case "cc_name":
      return;
    case "delivery_address":
      return;
    case "email":
    $email_record .= "E-mail: ".$v;
      return;
    default:
      if (strpos($name, "_line_cost") == false && strpos($name, "_item_stock") == false) {
        $record .= $name.": ".$v.", ";
      }
      return $name;
  }
}

$record = "Purchases: ";
$correct_values = true;
$item_quantity = 0;
$item = true;
$card_type = "no card type specified";
$newStock = [];
$total_cost_record = "";
$email_record = "";

if (empty($_POST)) {
  wrongInfo("No values given.");
}

foreach (array_keys($_POST) as $k) {
  global $newStock;
  global $card_type;
  global $item_quantity;
  global $correct_values;
  global $item;
  global $record;
  $display = true; // If this value is false, the value is not printed
  if ($correct_values) {
    $v = getFormInfo($k);
    if ($v == "" OR $v == NULL) { // Because the selected quantity cannot be empty in shopfront, this is not a problem
      wrongInfo("Missing value for ".$k);
      break;
    }
    switch ($k) {
      case "cc_number":
        testCardNumber($v);
        break;
      case "cc_code":
        testSecurityCode($v);
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
        $display = false;
        break;
      case "delivery_country": // This information is not displayed
        $display = false;
        break;
      default:
        break;
    }
    if ($item) {
      $item_quantity += $v;
    }
    if ($display AND $correct_values AND $v != "" AND $v != NULL) { //Treat numbers as Strings

      if (substr($k, -strlen("_item_stock")) == "_item_stock") {
        $k = formatNames($k, $v);
        $k = str_replace("_item_stock", "", $k);
        $newStock[$k] = $v; // Here the new stock of an item is stored
      } else if ($v != "0") {
        $k = formatNames($k, $v);
      }
    }
  }
}

// The stock values and the orders.txt files will not be updated if there is a bad input
if ($correct_values) {

  $record = $total_cost_record.", ".$record;
  $record = $email_record.", ".$record;

  $date = getdate();
  $dateString = $date["mday"].".".$date["mon"].".".$date["year"];
  $record = "Day: ".$dateString.", ".$record;

  $transaction_ID = strtoupper(uniqid());
  echo "<input type=\"hidden\" name=\"transaction_id\" value=\"$transaction_ID\">";
  //The ID is created on this page, in order to make sure it does not update when the receipt page is refreshed
  $record = "Transaction ID: ".$transaction_ID.", ".$record;

  updateStock();

  $record = substr($record, 0, strlen($record) - 2); // This removes the comma at the end
  $record .= "\n";

  updateRecord();

  // The post values are reused in the new form which is submitted
  foreach ($_POST as $k => $v) {
      echo '<input type="hidden" name="'.htmlentities($k).'" value="'.htmlentities($v).'">';
  }
}

?>
</form>
<script type="text/javascript">
  //Once the files have been updated, the form submits and the user is redirected to the receipt page
  if (!document.contains(document.getElementById("error"))) { // Will not submit if tests failed
    document.getElementById('form').submit();
  }
</script>

</body>

</html>
