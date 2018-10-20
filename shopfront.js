let subTotal = 0;
let vat = 0;
let delivery_charge = 0;
let totalCost = 0;

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
  updateSubTotal(item_id, "stock_item");
}

function updateSubTotal(element) {
 var total = 0;
 for (item_id of document.getElementsByTagName(element)) {
   //var i = document.getElementById(item_id);
   //var i = item_id.getElementById("item_price");

   total = total + getStockItemValue(item_id, "item_price");
 }
 this.subTotal = total; //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 document.getElementById("sub_total").innerHTML = total.toFixed(2);
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
