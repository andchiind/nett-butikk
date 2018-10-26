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
$card_type = "no card type specified";
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

$printout = "";
$correct_values = true;
$item_quantity = 0;
$item = true;
foreach (array_keys($_POST) as $k) {
  global $card_type;
  global $item_quantity;
  global $correct_values;
  global $item;
  //$correct_values = true;
  $display = true;

  if ($correct_values) {
    $v = getFormInfo($k);
    if ($v == "" OR $v == NULL) { //THIS MIGHT NOT BE USEFUL, SEE ITEM QUANTITY
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
      $printout = $printout."{$k} : {$v}<br />\n";
    }
  }
}

if ($correct_values) {
  $date = getdate();
  $transaction_ID = strtoupper(uniqid());
  echo "Transaction ID: ".$transaction_ID."<br />";
  echo "Date of transaction: ";
  echo $date["mday"].".".$date["mon"].".".$date["year"].".<br />";
  echo $printout."<br />";

  echo "<form name=\"order\" action=\"shopfront.php\" method=\"POST\"> <input type=\"submit\" value=\"Return to store\" /> </form>";
}

?>

</p>

</body>
</html>
