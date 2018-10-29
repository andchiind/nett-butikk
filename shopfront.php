<?php
clearstatcache(); // http://php.net/manual/en/function.clearstatcache.php
define("STOCK_FILE_NAME", "stock.txt"); // Local file - insecure!
define("STOCK_FILE_LINE_SIZE", 256); // 256 line length should enough.
define("PHOTO_DIR", "piks/large/"); // large photo, local files, insecure!
define("THUMBNAIL_DIR", "piks/thumbnail/"); // thumbnail, local files, insecure!

function photoCheck($photo) { // Do we have photos?
  $result = "";
  $p = PHOTO_DIR . $photo;
  $t = THUMBNAIL_DIR . $photo;
  if (!file_exists($p) || !file_exists($t)) { $result = "(No photo)"; }
  else { $result = "<a href=\"{$p}\"><img src=\"{$t}\" border=\"0\" /></a>"; }
  return $result;
}

if (!file_exists(STOCK_FILE_NAME)) {
  die("File not found for read - " . STOCK_FILE_NAME . "\n"); // Script exits.
}

$f = fopen(STOCK_FILE_NAME, "r");
$stock_list = null;
print_r($stock_list);
while (($row = fgetcsv($f, STOCK_FILE_LINE_SIZE)) != false) {
  $stock_item = array(
    "id" => $row[0], /// needs to be unique!
    "photo" => $row[0] . ".jpg",
    "name" => $row[1],
    "info" => $row[2],
    "price" => $row[3],
    "stock" => $row[4]);
  $stock_list[$row[0]] = $stock_item; // Add stock.
}

fclose($f);
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="shopfront.css" type="text/css" />
  <title>Items for sale</title>
</head>

<body>

<script src="shopfront.js"></script>

<h1>Items for Sale</h1>

<hr />

<form name="order" method="POST" id="form" onsubmit="confirmation(); return false;">

<stock_list>

  <stock_item>
    <item_photo class="heading">Photo</item_photo>
    <item_name class="heading">Name</item_name>
    <item_info class="heading">Description</item_info>
    <item_price class="heading"> &pound; (exc. VAT)</item_price>
    <item_quantity class="heading">Quantity</item_quantity>
    <line_cost class="heading">Cost</line_cost>
    <item_stock class="heading">Stock</item_stock>
  </stock_item>

<?php
foreach(array_keys($stock_list) as $id) {
  echo "  <stock_item id=\"{$id}\">\n";
  $item = $stock_list[$id];
  $p = photoCheck($item["photo"]);
  echo "    <item_photo>{$p}</item_photo>\n";
  echo "    <item_name>{$item["name"]}</item_name>\n";
  echo "    <item_info>{$item["info"]}</item_info>\n";
  echo "    <item_price>{$item["price"]}</item_price>\n";

  if ($item["stock"] == "0") {
  echo "    <item_quantity class=\"out_of_stock\">Out</item_quantity>\n";
  echo "    <line_cost class=\"out_of_stock\">of</line_cost>\n";
  echo "    <item_stock class=\"out_of_stock\">stock!</item_stock>\n";
  } else {

  echo "    <item_quantity value=\"0\"><input name=\"{$id}\" onclick=\"selectInput(this);\" onblur=\"unSelectInput(this);\" type=\"text\" value=\"0\" pattern=\"[0-9]+\" size=\"3\" onchange=\"updateLineCost(this, '{$id}');\" /></item_quantity>\n";
  echo "    <line_cost>0.00</line_cost>\n";
  echo "      <input type=\"hidden\" name=\"{$id}_line_cost\" value=\"0.00\" />\n";
  echo "    <item_stock class=\"stock\">{$item["stock"]}</item_stock>\n"; //This value is changed and displayed
  }

  echo "      <input type=\"hidden\" name=\"{$id}_item_stock\" value=\"{$item["stock"]}\" />";
  echo "      <total_stock style=\"display:none\">{$item["stock"]}</item_stock>\n"; //This stores the total stock
  echo "  </stock_item>\n\n";
}
?>

</stock_list>

<br />

<p>Sub-total: <span id="sub_total"></span></p>
<input type="hidden" name="sub_total" value="0.00" />

<p>Delivery charge: <span id="delivery_charge"></span></p>
<input type="hidden" name="delivery_charge" value="0.00" />

<p>VAT: <span id="vat"></span></p>
<input type="hidden" name="vat" value="0.00" />

<p>Total: <span id="total"></span></p>
<input type="hidden" name="total" value="0.00" />
<hr />

<form_input id="formInput">

  <p>Credit Card type:
    <select name="cc_type" size="1" required>
      <option value="" selected>-</option>
      <option value="mastercard">MasterCard</option>
      <option value="visa">Visa</option>
    </select>
  </p>

  <p>Credit Card number:
    <input type="text" name="cc_number" pattern="[0-9]{16}" size="16" onchange="checkCard();" required/></p>

  <p>Name on Credit Card (also the name for delivery):
    <input type="text" name="cc_name" size="80" required/></p>

  <p>Credit Card security code:
    <input type="text" name="cc_code" pattern="[0-9]{3}" size="3" required/></p>

  <p>Delivery street address:
    <input type="text" name="delivery_address" size="128" required/></p>

  <p>Delivery postcode:
    <input type="text" name="delivery_postcode" size="40" required/></p>

  <p>Delivery country:
    <input type="text" name="delivery_country" size="80" required/></p>

  <p>Email:
    <input type="email" name="email" required/></p>

<hr />

<input id="form_button" type="submit" class="button" value="Place Order" />

</form_input>

</form>

<div id="confirm"></div>

<hr />

</body>
</html>
