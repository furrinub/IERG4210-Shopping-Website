<?php
/* @TODO It is free to add helper functions here. */
/* ========== REGION START ========== */
session_start();
include_once('lib/db.inc.php');

/* ========== REGION END ========== */

/**
 * This function returns a digest based on a list of variables.
 * @return a string denoted digest
 */
function gen_digest($array) {
    return hash("sha256", implode(";", $array));
}

/**
 * This function returns a UUID v4.
 * @return a string denoted UUID v4
 * @see https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
 */
function gen_uuid() {
    $data = random_bytes(16);
    $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    $uuid[14] = '4';
    $uuid[19] = ['8', '9', 'a', 'b'][random_int(0, 3)];
    return $uuid;
}

/**
 * Returns an valid order with digest and invoice.
 * @param an object representing items in cart (pid + quantity)
 * @return a string representing the valid order
 */
function create_order($cart) {
    /* What I did:
     * Comment out the current return statement
     * Comment out the gen_digest statement
     * Change the json HERE string into single quote so that PHP7.2 wont have parse error
     */
    /* ========== REGION START ========== */
    $json = '
    {
        "purchase_units": [
            {
                "amount": {
                    "currency_code": "HKD",
                    "value": 5,
                    "breakdown": {
                        "item_total": {
                            "currency_code": "HKD",
                            "value": 5
                        }
                    }
                },
            "items": [
                {
                    "name": "1:ProductA",
                    "unit_amount": {
                        "currency_code": "HKD",
                        "value": 1
                    },
                    "quantity": 1
                },
                {
                    "name": "2:ProductB",
                    "unit_amount": {
                        "currency_code": "HKD",
                        "value": 2
                    },
                    "quantity": 2
                }
            ]
            }
        ]
    }';

    $order = json_decode($json);

    //$order->purchase_units[0]->custom_id = gen_digest(array($order->purchase_units[0]->amount->currency_code));
    $order->purchase_units[0]->invoice_id = gen_uuid(); // invoice_id must be unique to avoid crashes.

    //return json_encode($order);

    /* ========== REGION END ========== */
    /* @TODO Your Implementation here */
    /* ========== REGION START ========== */


    $products = ierg4210_prod_fetchAll();
    $total_price = 0;
    $purchase_units_items = array();
    $digest_input = array();
    
    // calculate digest
    foreach ($cart as $i) {
        $prod = ierg4210_prod_fetchOne($i->pid);
        array_push($purchase_units_items, array(
            "name" => "{$i->pid}:$prod[NAME]",
            "unit_amount" => array(
                "currency_code" => "HKD",
                "value" => intval($prod["PRICE"])
            ),
            "quantity" => intval($i->quantity)
        ));
        $total_price += $prod['PRICE'] * $i->quantity;
        // digest consists of: pid and quantity and price/item of each prod, total price, currency type, Merchant’s email address, random salt
        array_push($digest_input, $i->pid, $i->quantity, $prod['PRICE']);
    }
    array_push($digest_input, 
        $total_price,
        $order->purchase_units[0]->amount->currency_code,
        json_decode(file_get_contents("/var/www/secret.json"))->merchant_email,
        gen_uuid() // random salt
    );
    
    // set price and digest
    $digest = gen_digest(array_map('strval', $digest_input)); // convert into array of str
    $order->purchase_units[0]->amount->value = $total_price;
    $order->purchase_units[0]->amount->breakdown->item_total->value = $total_price;
    $order->purchase_units[0]->items = $purchase_units_items;
    $order->purchase_units[0]->custom_id = $digest;

    return json_encode($order);

    /* ========== REGION END ========== */
}


$json = file_get_contents("php://input");
$cart = json_decode($json);
header('Content-Type: application/json', true);
echo create_order($cart);
