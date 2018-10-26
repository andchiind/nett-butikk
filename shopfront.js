/*
 * item_id: string (id of item)
 * element: string (tag name of element)
 */
function getStockItemValue(item_id, element) {

  //console.log(item_id.id);
  var i = document.getElementById(item_id);
  var e = i.getElementsByTagName(element)[0];  // assume only 1!
  var v = e.innerHTML;
  return v;
}

/*
 * item_id: string (id of item)
 * element: string (tag name of element)
 * value: string (the value of the element)
 */
function setStockItemValue(item_id, element, value) {
  var i = document.getElementById(item_id);
  var e = i.getElementsByTagName(element)[0];  // assume only 1!
  e.innerHTML = value;
}

function confirmation() {

  let inputs = document.getElementById("formInput");
  let input = inputs.getElementsByTagName("p");
  let items = document.getElementsByTagName("stock_item");

  inputs.style.display = "none";

  let newForm = "<h2>Are you sure that the following information is correct?</h2>";

  for (let i = 0; i < items.length; i++) {

    let price = items[i].getElementsByTagName("line_cost")[0].innerHTML;
    let name = items[i].getElementsByTagName("item_name")[0].innerHTML;

    if (price != "0.00" && name != "Name") {

      let quantityTag = items[i].getElementsByTagName("item_quantity")[0];
      let quantity = quantityTag.children[0].value;

      newForm += "<p>" + name + ": " + quantity + "<br /> Price: " + price + "</p>";
    }
  }

  //if (document.getElementById("total").innerHTML != UNDEFINED) { !!!!!!!!!!!!!!!!!!
  newForm += "<p>Sub-total: " + document.getElementById("sub_total").innerHTML + "<br />";
  newForm += "Delivery charge: " + document.getElementById("delivery_charge").innerHTML + "<br />";
  newForm += "VAT: " + document.getElementById("vat").innerHTML + "<br />";
  newForm += "Total Cost: " + document.getElementById("total").innerHTML + "</p>";
  //}

  for (let i = 0; i < input.length; i++) {
    let p = input[i].childNodes[0].nodeValue;
    let inputContent = "";
    if (input[i].children[0].tagName == "select") {
      inputContent = input[i].children[0].options[children[0].selectedIndex].value;
    } else {
      inputContent = input[i].children[0].value;
    }
    newForm += p + " " + inputContent + "<br />";
  }

  newForm += "<br />";

  newForm += "<input type=\"submit\" value=\"Confirm\" onclick=\"openReceipt();\" class=\"button\" />";
  newForm += "<input type=\"button\" value=\"Cancel\" onclick=\"returnToShop();\" class=\"button\" />";

  document.getElementById("confirm").innerHTML = newForm;
  document.getElementById("confirm").style.display = "block";
}

function openReceipt() {
  let form = document.getElementById("form");
  form.onsubmit = "";
  form.action = "shopback.php";
  form.submit();
}

function returnToShop() {
  let confirmInfo = document.getElementById("confirm");
  confirmInfo.innerHTML = "";
  confirmInfo.style.display = "none";

  let form = document.getElementById("form");
  form.action = "";

  let inputs = document.getElementById("formInput");
  inputs.style.display = "block";
}

function checkCard() {
  let cardNumber = document.getElementsByName("cc_number")[0];
  let cardTypeSelect = document.getElementsByName("cc_type")[0];
  let cardType = cardTypeSelect.options[cardTypeSelect.selectedIndex].value;
  let button = document.getElementById("form_button");

  if ((cardNumber.value.substring(0,1) == "4" && cardType == "visa")
  || (cardNumber.value.substring(0,1) == "5" && cardType == "mastercard")) {
    button.disabled = false;
  } else {
    button.disabled = true;
    alert("The card number should start with '5' for mastercard or '4' for visa.");
  }
}

/*
 * e: object from DOM tree (item_quantity that made )
 * item_id: string (id of item)
 */
function updateLineCost(e, item_id) {
  var p = getStockItemValue(item_id, "item_price");
  var q = e.value;
  if (q == "") {
    q = 0;
    e.value = q; // This ensures that the input is always a number
  }
  if ((q % 1) == 0) { //Only allows integers as item quantitites
    if (updateStock(q, item_id)) {
      var c = p * q; // implicit type conversion
      c = c.toFixed(2); // 2 decimal places always.

      var input = document.getElementsByName(item_id + "_line_cost")[0];
      input.value = c;

      setStockItemValue(item_id, "line_cost", c);
      updateSubTotal();
      updateVAT();
      updateDeliveryCharge();
      updateTotalCost();
    }
  }
}

function updateStock(q, item_id) {

  var item = document.getElementById(item_id);
  var stock = item.getElementsByTagName("item_stock")[0];
  //var totalStock = item.getElementsByTagName("total_stock")[0];
  console.log(item);
  console.log(stock.getName());
  //console.log(totalStock);
  let newStock = parseFloat(stock.name) - parseFloat(q);
  if (newStock >= 0) {
    stock.innerHTML = parseFloat(stock.name) - parseFloat(q);
    return true;
  } else {
    alert("Insufficient stock, please select a lower quantity");
    return false;
  }
}

function updateSubTotal() {

  var s = parseFloat(document.getElementById("sub_total").innerHTML);
  var l = document.getElementsByTagName("line_cost");
  var subTotal = 0;

  for (let i = 0; i < l.length; i++) {
    let p = parseFloat(l[i].innerHTML);
    if (p > 0) {
      subTotal += p;
    }
  }

  let st = document.getElementById("sub_total");

  st.innerHTML = subTotal.toFixed(2);
  updateHiddenInput(st);
}

function updateDeliveryCharge() {

  var st = parseFloat(document.getElementById("sub_total").innerHTML);
  var delivery_charge = st / 10;
  var d = document.getElementById("delivery_charge");

  d.innerHTML = delivery_charge.toFixed(2);
  updateHiddenInput(d);
}

function updateVAT() {

  var st = parseFloat(document.getElementById("sub_total").innerHTML);
  var vat = 0;
  if (st < 100) {
    vat = st / 5;
  }

  var v = document.getElementById("vat");

  v.innerHTML = vat.toFixed(2);
  updateHiddenInput(v);
}

function updateTotalCost() {

  var st = parseFloat(document.getElementById("sub_total").innerHTML);
  var v = parseFloat(document.getElementById("vat").innerHTML);
  var d = parseFloat(document.getElementById("delivery_charge").innerHTML);

  var totalCost = v + d + st;

  var t = document.getElementById("total");

  t.innerHTML = totalCost.toFixed(2);

  updateHiddenInput(t);
}

function updateHiddenInput($id) {
  let value = $id.innerHTML;
  let input = document.getElementsByName($id.id)[0];
  input.value = value;
}
