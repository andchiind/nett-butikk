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
  let form = document.getElementById("form");

  form.style.visibility = "hidden";
  //form.style.display = "none";

  let inputs = document.getElementById("formInput");
  let input = inputs.getElementsByTagName("p");
  let items = document.getElementsByTagName("stock_item");

  let newForm = "<h2>Are you sure that the following information is correct?</h2>";

  for (let i = 0; i < items.length; i++) {
    let quantity = items[i].getElementsByTagName("item_quantity")[0].value;
    let price = items[i].getElementsByTagName("line_cost")[0].innerHTML;
    let name = items[i].getElementsByTagName("item_name")[0].innerHTML;
    if (price != "0.00" && name != "Name") {
      newForm += "<p>" + name + ": " + quantity + "</ br>Price: " + price + "</p>";
    }
  }

  for (let i = 0; i < input.length; i++) {
    let p = input[i].childNodes[0].nodeValue;
    let inputContent = "";
    if (input[i].children[0].tagName == "select") {
      inputContent = input[i].children[0].options[children[0].selectedIndex].value;
    } else {
      inputContent = input[i].children[0].value;
    }
    newForm += "<p>" + p + " " + inputContent + "</p>";
  }

  newForm += "<input type=\"submit\" value=\"Confirm\" class=\"button\" />";
  newForm += "<input type=\"button\" value=\"Cancel\" onclick=\"returnToShop();\" class=\"button\" />";

  console.log("hei hei hei");
  document.getElementById("confirm").innerHTML = newForm;
  console.log("???");
  //document.getElementById("confirm").style.display = "block";
  document.getElementById("confirm").style.visibility = "visible";
}

function returnToShop() {
  let form = document.getElementById("confirm");
  let oldForm = document.getElementById("form");
  oldForm.style.visibility = "visible";
  //oldForm.style.display = "block";

  form.innerHTML = "";

  form.style.visibility = "hidden";
  //form.style.display = "none";
}

/*
 * e: object from DOM tree (item_quantity that made )
 * item_id: string (id of item)
 */
function updateLineCost(e, item_id) {
  var p = getStockItemValue(item_id, "item_price");
  var q = e.value;
  var c = p * q; // implicit type conversion
  c = c.toFixed(2); // 2 decimal places always.
  setStockItemValue(item_id, "line_cost", c);
  updateSubTotal();
  updateVAT();
  updateDeliveryCharge();
  updateTotalCost();
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

  document.getElementById("sub_total").innerHTML = subTotal.toFixed(2);
}

// function getItemsAndPrice() {
//   var returnString;
//   for (var key in items) {
//     if (items.hasOwnProperty(key)) {
//       returnString = returnString + "<br />" + key + ": " + items[key];
//     }
//   }
//   document.getElementById("items").innerHTML = returnString;
// }

function updateDeliveryCharge() {

  var st = parseFloat(document.getElementById("sub_total").innerHTML);
  var delivery_charge = st / 10;
  var d = document.getElementById("delivery_charge");

  d.innerHTML = delivery_charge.toFixed(2);
}

function updateVAT() {

  var st = parseFloat(document.getElementById("sub_total").innerHTML);
  var vat = st / 5;
  var v = document.getElementById("vat");

  v.innerHTML = vat.toFixed(2);
}

function updateTotalCost() {

  var st = parseFloat(document.getElementById("sub_total").innerHTML);
  var v = parseFloat(document.getElementById("vat").innerHTML);
  var d = parseFloat(document.getElementById("delivery_charge").innerHTML);

  var totalCost = v + d + st;

  var t = document.getElementById("total");

  t.innerHTML = totalCost.toFixed(2);
}
//
// function getTotalCost() {
//  var tc = document.getElementById("total_cost");
//  console.log(totalCost);
//  tc.innerHTML = totalCost;
// }
//
// function getDeliveryCharge() {
//   var dc = document.getElementById("delivery_charge")
//   dc.innerHTML = delivery_charge;
// }
//
// function getSubTotal() {
//   var st = document.getElementById("total");
//   st.innerHTML = subTotal;
// }
//
// function getVAT() {
//   var v = document.getElementById("vat");
//   v.innerHTML = vat;
// }
