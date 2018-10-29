<form id="form" action="shopback.php" method="post">

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

function updateRecord() {
  global $record;
  define("RECORD_FILE_NAME", "orders.txt");
  define("RECORD_FILE_LINE_SIZE", 256);
  if (!file_exists(RECORD_FILE_NAME)) {
    die("File not found for read - " . RECORD_FILE_NAME . "\n");
  }

  $f = RECORD_FILE_NAME;

  file_put_contents($f, $record, FILE_APPEND);
}

function getFormInfo($k) {
  return isset($_POST[$k]) ? htmlspecialchars($_POST[$k]) : null;
}

function wrongInfo($error) { // This standard for errors makes the program easy to expand
  global $correct_values;
  echo "The given information is not correct. <br />";
  echo $error."<br /><br />";
  $correct_values = false;
  echo "<form id=\"order\" action=\"shopfront.php\" method=\"POST\"> <input type=\"submit\" value=\"Return to store\" /> </form>";
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

function formatNames($name, $v) {
  global $record;
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
      if (strpos($name, "_line_cost") == false && strpos($name, "_item_stock") == false) {
        $record .= $name.": ".$v.", ";
      }
      return $name;
  }
}

$record = "Item quantity: ";
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
  global $record;
  $display = true;
  if ($correct_values) {
    $v = getFormInfo($k);
    if ($v == "" OR $v == NULL) { // Because the selected quantity cannot be empty in shopfront, this is not a problem
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
        $newStock[$k] = $v;
      } else if ($v != "0") {
        $k = formatNames($k, $v);
      }
    }
  }
}

// The stock values and the orders.txt files will not be updated if there is a bad input
if ($correct_values) {

  $date = getdate();
  $dateString = $date["mday"].".".$date["mon"].".".$date["year"];
  $record = "Day: ".$dateString.", ".$record;

  $transaction_ID = strtoupper(uniqid());
  echo "<input type=\"hidden\" name=\"transaction_id\" value=\"$transaction_ID\">";
  $record = "Transaction ID: ".$transaction_ID.", ".$record;

  updateStock();

  $record = substr($record, 0, strlen($record) - 2); // This removes the comma at the end
  $record .= "\n";

  updateRecord();

  foreach ($_POST as $a => $b) {
      echo '<input type="hidden" name="'.htmlentities($a).'" value="'.htmlentities($b).'">';
  }

}

?>
</form>
<script type="text/javascript">
  if (!document.contains(document.getElementById("order"))) {
    document.getElementById('form').submit();
  }
</script>
