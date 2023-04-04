<?php
/* @TODO It is free to add helper functions here. */
/* ========== REGION START ========== */
session_start();
include_once('lib/db.inc.php');

/* ========== REGION END ========== */

/**
* This function saves the order into the databse.
* @param order an object containing order details
*/
function save_order($order) {
    /* @TODO Comment out the current return statement */
    /* ========== REGION START ========== */
    file_put_contents("order.json", json_encode($order, JSON_PRETTY_PRINT));
    //echo json_encode($order);
    /* ========== REGION END ========== */
    
    /* @TODO Your Implementation Here. */
    /* ========== REGION START ========== */

    /*
    CREATE TABLE orders (
        TRANSACTION_ID INTEGER PRIMARY KEY,
        EMAIL TEXT,
        INVOICE_ID TEXT, // uuid v4
        COMPLETE_TIME INTEGER, // unix timestamp
        ORDER_STATUS TEXT, // should be COMPLETED
        TOTAL_PRICE REAL,
        ITEM_COUNT INTEGER,
        ITEMS TEXT // json
    );

    ITEMS is json [{name, price, quantity}]
    */

    $email = $_SESSION['auth']['email'] ?? 'guest';
    $invoice_id = $order->purchase_units[0]->invoice_id;
    $complete_time = time(); // unix timestamp
    $status = $order->status;
    $total_price = floatval($order->purchase_units[0]->amount->value);
    $item_count = 0;
    $items = array();
    foreach ($order->purchase_units[0]->items as $i) {
        $name = explode(":", $i->name, 2)[1];
        $quantity = intval($i->quantity);
        $unit_price = floatval($i->unit_amount->value);
        array_push($items, array("name" => $name, "quantity" => $quantity, "price" => $unit_price));
        $item_count += $quantity;
    }
    
    order_insert($email, $invoice_id, $complete_time, $status, $total_price, $item_count, json_encode($items));
    /*
        $date = new DateTime();
        $date->setTimestamp($timestamp);
        $date->setTimezone(new DateTimeZone('Asia/Hong_Kong'));
        $formatted_date = $date->format('Y-m-d H:i:s');
     */
    
    /* ========== REGION END ========== */
}

$json = file_get_contents("php://input");
$order = json_decode($json);
save_order($order);
