/*
 * item_id: string (id of item)
 * element: string (tag name of element)
 */
function getStockItemValue(item_id, element) {

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

/*
 * This function hides the form and prints out the confirmation details, as well
 * as two buttons which allows the user to confirm the purchase or go back to edit
 * the transaction details.
 */
function confirmation() {

  let inputs = document.getElementById("formInput");
  let input = inputs.getElementsByTagName("p");
  let items = document.getElementsByTagName("stock_item");

  inputs.style.display = "none";

  let newForm = "";

  if (document.getElementById("total").innerHTML > 0) {

    newForm = "<h2>Are you sure that the following information is correct?</h2>";

    for (let i = 0; i < items.length; i++) {

      let price = items[i].getElementsByTagName("line_cost")[0].innerHTML;
      let name = items[i].getElementsByTagName("item_name")[0].innerHTML;

      if (price != "0.00" && name != "Name" && price != undefined) {

        let quantityTag = items[i].getElementsByTagName("item_quantity")[0];
        if (quantityTag.children.length > 0) {
          let quantity = quantityTag.children[0].value;

          newForm += "<p>" + name + ": " + quantity + "<br /> Price: " + price + "</p>";
        }
      }
    }

    newForm += "<p>Sub-total: " + document.getElementById("sub_total").innerHTML + "<br />";
    newForm += "Delivery charge: " + document.getElementById("delivery_charge").innerHTML + "<br />";
    newForm += "VAT: " + document.getElementById("vat").innerHTML + "<br />";
    newForm += "Total Cost: " + document.getElementById("total").innerHTML + "</p>";

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

  } else {
    newForm += "It seems that you have not selected any items. <br /> Please do so before moving on. <br /><br />"
    newForm += "<input type=\"button\" value=\"Cancel\" onclick=\"returnToShop();\" class=\"button\" />";
  }

  document.getElementById("confirm").innerHTML = newForm;
  document.getElementById("confirm").style.display = "block";

}

/*
 * Submits the information in the form to the shopRedirect.php file.
 */
function openReceipt() {
  let form = document.getElementById("form");
  form.onsubmit = "";
  form.action = "shopRedirect.php";
  form.submit();
}

/*
 * Hides the confirmation information and un-hides the form.
 */
function returnToShop() {
  let confirmInfo = document.getElementById("confirm");
  confirmInfo.innerHTML = "";
  confirmInfo.style.display = "none";

  let form = document.getElementById("form");
  form.action = "";

  let inputs = document.getElementById("formInput");
  inputs.style.display = "block";
}

/*
 * This function checks that the card number start with the correct number
 * corresponding with the card type. If the card number is wrong, an alert is
 * given and the confirm buttong is disabled.
 */
function checkCard() {
  let cardNumber = document.getElementsByName("cc_number")[0];
  let cardTypeSelect = document.getElementsByName("cc_type")[0];
  let cardType = cardTypeSelect.options[cardTypeSelect.selectedIndex].value;
  let button = document.getElementById("form_button");

  if ((cardNumber.value.substring(0,1) == "4" && cardType == "visa")
  || (cardNumber.value.substring(0,1) == "5" && cardType == "mastercard")
  || cardNumber.value.length == 0) {
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
  if (q == "" || q < 0 || (q % 1) != 0) { //Only allows positive integers as item quantitites
    q = 0;
    e.value = q; // This ensures that the input is always a number
  }
  if (updateStock(q, item_id)) {
    var c = p * q; // implicit type conversion
    c = c.toFixed(2); // 2 decimal places always.

    var input = document.getElementsByName(item_id + "_line_cost")[0];
    input.value = c;

    setStockItemValue(item_id, "line_cost", c);
    updateSubTotal();
    updateDeliveryCharge();
    updateVAT();
    updateTotalCost();
  }

}

/*
 * Updates the stock value of the hidden input tag being submitted. If the
 * quantity selected is too high an alert is sent and the function returns false.
 * q: number (item quantity)
 * item_id: string (id of given item)
 */
function updateStock(q, item_id) {

  var item = document.getElementById(item_id);
  var stock = item.getElementsByTagName("item_stock")[0];
  var totalStock = item.getElementsByTagName("total_stock")[0];
  let newStock = parseFloat(totalStock.innerHTML) - parseFloat(q);
  if (newStock >= 0) {
    stock.innerHTML = newStock;

    var input = document.getElementsByName(item_id + "_item_stock")[0];
    input.value = newStock;
    return true;
  } else {
    alert("Insufficient stock, please select a lower quantity");
    return false;
  }
}

/*
 * Updates the sub-total value that is displayed, as well as the value of the
 * hidden input tag which is submitted.
 */
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

/*
 * Updates the delivery-charge value that is displayed, as well as the value of the
 * hidden input tag which is submitted.
 */
function updateDeliveryCharge() {

  var st = parseFloat(document.getElementById("sub_total").innerHTML);

  var delivery_charge = 0;
  if (st == 0 || st < 0) {
    delivery_charge = 0;
  } else if (st < 100) { //The delivery is free if the sub-total is greater than 100
    delivery_charge = st / 10;
  }

  var d = document.getElementById("delivery_charge");

  d.innerHTML = delivery_charge.toFixed(2);
  updateHiddenInput(d);
}

/*
 * Updates the VAT value that is displayed, as well as the value of the
 * hidden input tag which is submitted.
 */
function updateVAT() {

  var st = parseFloat(document.getElementById("sub_total").innerHTML);
  var dc = parseFloat(document.getElementById("delivery_charge").innerHTML);
  var vat = 0;

  if (st == 0 || st < 0) {
    vat = 0;
  } else {
    vat = (st + dc) / 5;
  }

  var v = document.getElementById("vat");

  v.innerHTML = vat.toFixed(2);
  updateHiddenInput(v);
}

/*
 * Updates the total value that is displayed, as well as the value of the
 * hidden input tag which is submitted.
 */
function updateTotalCost() {

  var st = parseFloat(document.getElementById("sub_total").innerHTML);
  var v = parseFloat(document.getElementById("vat").innerHTML);
  var d = parseFloat(document.getElementById("delivery_charge").innerHTML);

  var totalCost = v + d + st;

  var t = document.getElementById("total");

  t.innerHTML = totalCost.toFixed(2);

  updateHiddenInput(t);
}

/*
 * Updates the value of the hidden input tag which is submitted, correpsonding
 * with the given element.
 * this_id: DOM element (element which is being updated)
 */
function updateHiddenInput(this_id) {
  let value = this_id.innerHTML;
  let input = document.getElementsByName(this_id.id)[0];
  input.value = value;
}

/*
 * If the value in the input box is zero, it is cleared when the user selects it.
 */
function selectInput(input_box) {
  let value = input_box.value;
  if (value == "0") {
    input_box.value = "";
  }
}

/*
 * If the input box is empty, it is assigned to 0 when the user un-selects it.
 */
function unSelectInput(input_box) {
  let value = input_box.value;
  if (value == "") {
    input_box.value = "0";
  }
}
