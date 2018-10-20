<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Receipt</title>
</head>

<body>

<script src="shopfront.js"></script>

<h1>Digital Receipt</h1>

<p>
  <script src="shopfront.js"></script>

<?php

$card_type = "no card type specified";

function getFormInfo($k) {
  return isset($_POST[$k]) ? htmlspecialchars($_POST[$k]) : null;
}

function wrongInfo($error) {
  global $correct_values;
  echo "The given info is wrong, I'm afraid. <br />";
  echo $error."<br />";
  echo "Please try again, idiot. <br /><br />";
  $correct_values = false;
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

function getStockItemValue($k) {

}

$printout = "";
$item_quantity = 0;
$correct_values = true;
foreach (array_keys($_POST) as $k) {
  global $card_type;
  global $item_quantity;
  global $correct_values;
  if ($correct_values) {
    $v = getFormInfo($k);
    if ($v == "" OR $v == NULL) {
      wrongInfo("Missing value for ".$k);
      break;
    }
    switch ($k) {
      case "cc_number":
        testCardNumber($v);
        break;
      case "cc_code":
        testSecurityCode($v);
        break;
      case "cc_type":
        if ($v == "visa") {
          $card_type = "visa";
        } else {
          $card_type = "master";
        }
        break;
      case "item_quantity":
        testItemQuantity($v);
        break;
      default:
        break;
    }
    if ($correct_values AND $v != "" AND $v != NULL AND $v != "0") { //Treat numbers as Strings
      $printout = $printout."{$k} : {$v}<br />\n";



      $itemValue = "<p value=\"getStockItemValue({$k}, \"item_price\");\" />";
      //if ($itemValue != 0 AND $itemValue != NULL AND $itemValue != "") {
      $printout = $printout." Price of all {$k}'s: {$itemValue}";
      //}
    }
  }
}

if ($correct_values) {
  $date = getdate();
  $transaction_ID = strtoupper(uniqid());
  echo "Transaction ID: ".$transaction_ID."<br />";
  echo "Date of transaction: ";
  echo $date["mday"].".".$date["mon"].".".$date["year"].".<br />".$printout;
  echo "<total_cost name=\"total_cost\" value=\"getTotalCost();\"></total_cost> <br />";
  echo "<vat name=\"vat\" value=\"getVAT();\"></vat> <br />";
  echo "<sub_total name=\"sub_total\" value=\"getSubTotal();\"></sub_total> <br />";
}

?>

</p>

</body>
</html>
