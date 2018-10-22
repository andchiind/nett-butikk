let subTotal = 0;
let vat = 0;
let delivery_charge = 0;
let totalCost = 0;
let items = [];

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
 * e: object from DOM tree (item_quantity that made )
 * item_id: string (id of item)
 */
function updateLineCost(e, item_id) {
  var p = getStockItemValue(item_id, "item_price");
  var q = e.value;
  var c = p * q; // implicit type conversion
  c = c.toFixed(2); // 2 decimal places always.
  setStockItemValue(item_id, "line_cost", c);
  items.push(item_id : c);
  updateSubTotal(item_id);
}

function updateSubTotal(element) {
 var total = 0;
 var i = document.getElementById("costs");
 var t = i.getElementById("sub_total_p");
 var y = t.getElementById("sub_total");
 for (item_id of document.getElementsByTagName("stock_item")) {
   var p = getStockItemValue(item_id, "item_price");
   var q = item_id.getElementsByTagName("item_quantity")[0];
   total = total + (p * q);

   //total = total + getStockItemValue(item_id, "item_price");
 }
 this.subTotal = total; //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 y.innerHTML = total.toFixed(2);
 //document.getElementById("sub_total").innerHTML = total.toFixed(2);
}

function getItemsAndPrice() {
  var returnString;
  for (var key in items) {
    if (items.hasOwnProperty(key)) {
      returnString = returnString + "<br />" + key + ": " + items[key];
    }
  }
  return returnString;
}

// function updateDeliveryCharge() {
//
// }
//
// function updateVAT() {
//
// }
//
// function updateTotalCost() {
//
// }
//
 function getTotalCost() {
   document.getElementById("total_cost").innerHTML = totalCost;
   return totalCost;
 }
//
 function getSubTotal() {
   return subTotal;
 }
//
 function getVAT() {
   return vat;
 }
