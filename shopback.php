
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Receipt</title>
</head>

<body>

<h1>Digital Receipt</h1>

<p>

<?php

function getFormInfo($k) {
  return isset($_POST[$k]) ? htmlspecialchars($_POST[$k]) : null;
}

/*
* This function formats the key values into more presentable Strings
*/
function formatNames($name) {
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
    case "transaction_id":
      return "Transaction ID";
    default:
      return $name;
  }
}

$printout = "";
$item_quantity = 0;
$item = true;
$card_type = "no card type specified";
$correct_values = true;

if (empty($_POST)) {
  global $correct_values;
  echo "<p>";
  echo "The given information is not correct. <br />";
  echo "No values given.<br /><br />";
  echo "</p>";
  $correct_values = false;
  echo "<error id=\"error\"></error>";
  echo "<form id=\"order\" action=\"shopfront.php\" method=\"POST\"> <input type=\"submit\" value=\"Return to store\" /> </form>";
}

foreach (array_keys($_POST) as $k) {
  global $card_type;
  global $item_quantity;
  global $item;
  $display = true;

  $v = getFormInfo($k);
  if ($v == "" OR $v == NULL) { // Because the selected quantity cannot be empty in shopfront, this is not a problem
    wrongInfo("Missing value for ".$k);
    break;
  }
  switch ($k) {
    case "cc_number":
      $beginning = substr($v, 0, 2);
      $ending = substr($v, strlen($v) - 2, strlen($v) - 1);
      $v = $beginning."************".$ending; //Hides parts of card number
      break;
    case "cc_code":
      $display = false;
      break;
    case "cc_type":
      $item = false;
      if ($v == "visa") {
        $card_type = "visa";
      } else {
        $card_type = "master";
      }
      break;
    case "item_quantity":
      break;
    case "delivery_postcode": // This information is not displayed
      $display = false;
      break;
    case "delivery_country": // This information is not displayed
      $display = false;
      break;
    case "transaction_id":
      $v .="<br />";
    default:
      break;
  }
  if ($item && substr($k, -strlen("transaction_id")) != "transaction_id") {
    $item_quantity += $v;
  }
  if ($correct_values AND $display AND $v != "" AND $v != NULL AND $v != "0") { //Treat numbers as Strings

    $k = formatNames($k);

    if (substr($k, -strlen("_line_cost")) == "_line_cost") { // Returns false if $k does not contain "line_cost"
      $k = "Item Cost:";
      $v = $v." <br />";
    }
    if (substr($k, -strlen("_item_stock")) == "_item_stock") { // Returns false if $k does not contain "line_cost"
      continue; //This information is not printed
    }

    if ($k == "total") {
      $v .= "<br />";
    }

    $printout = $printout."{$k} : {$v}<br />\n";
  }
}

if ($correct_values) {

  $date = getdate();
  $dateString = $date["mday"].".".$date["mon"].".".$date["year"];

  echo "Date of transaction: ";
  echo $dateString."<br />";
  echo $printout."<br />";
  echo "<form name=\"order\" action=\"shopfront.php\" method=\"POST\"> <input type=\"submit\" value=\"Return to store\" /> </form>";

}
?>

</p>

</body>
</html>
