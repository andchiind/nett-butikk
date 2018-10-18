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

$card_type = "";

foreach (array_keys($_POST) as $k) {
  $v = getFormInfo($k);
  if ($k == "cc_number") {
    $first_char = substr($v, 0, 1);
    if ($card_type == "visa" and $first_char != "4") {
      echo "baaaaaaaaaaaaaaaaaaad fooooooooooooooorm";
    } elseif ($card_type == "master" and $first_char != "5") {
      echo "baaaaaaaaaaaaaaaaaaaaaaaaaad maaaastercard";
    } else {
      echo "suuuuuuccceesssssssss";
    }
  }
  if ($k == "cc_type") {
    if ($v == "visa") {
      $card_type = "visa";
    } else {
      $card_type = "master";
    }
  }
  echo "{$k} : {$v}<br />\n";
}
?>
</p>

</body>
</html>
