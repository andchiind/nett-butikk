<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Receipt</title>
</head>

<body>

<h1>Receipt -- PHP yet to be completed!</h1>

<p>
<?php
// http://php.net/manual/en/function.htmlspecialchars.php
function getFormInfo($k) {
  return isset($_POST[$k]) ? htmlspecialchars($_POST[$k]) : null;
}

//$card_type = ""; ??????????????????????????????

function wrongInfo($error) {
  echo "The given info is wrong, I'm afraid. <br />";
  echo "The problem is: ".{$error}."<br />";
  echo "Please try again, idiot.";
}

function testCardNumber($v) {
  $first_char = substr($v, 0, 1);
  if ($card_type == "visa" AND $first_char != "4") {
    wrongInfo("A VISA card number should start with 4.");
  } elseif ($card_type == "master" AND $first_char != "5") {
    wrongInfo("A MASTERCARD card number should start with 5.");
  }
}

function testSecurityCode($v) {
  if ($v.length != 3 OR $v < 0) {
    wrongInfo("A security code should be three digits and positive.")
  }
}

function testItemQuantity($v) {
  if ($v < 1 OR strpos($v, ".") !== true) { //Check use of double ==. !!!!!!!!!!!
    wrongInfo("The quantity of items selected should be a positive integer.")
  }
}

$printout = ";"

foreach (array_keys($_POST) as $k) {
  $v = getFormInfo($k);
  if ($k == "cc_number") {
    testCardNumber($v);
  }
  if ($k == "cc_code") {
    testSecurityCode($v);
  }
  if ($k == "item_quantity") {
    testItemQuantity($v);
  }
  if ($k == "cc_type") {
    if ($v == "visa") {
      $card_type = "visa";
    } else {
      $card_type = "master";
    }
  }
  $printout = $printout."{$k} : {$v}<br />\n";
  //echo "{$k} : {$v}<br />\n";
}

echo $printout;

?>

</p>

</body>
</html>
