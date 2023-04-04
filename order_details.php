---
---
<?php
session_start();
include_once('lib/db.inc.php');
include_once('lib/auth.inc.php');

// if havent log in
if (!($auth_info = auth())) {
	header('Location: login.php', true, 302);
	exit();
}
$transcationId = $_GET['id'] ?? '';
$order = order_fetchOne($transcationId);
if (!$order) {
    throw new Exception('cannot find the order');
}
// cant view others orders
if (!$_SESSION['auth']['admin'] && $order['EMAIL'] !== $_SESSION['auth']['email']) {
    header('Location: index.php', true, 302);
	exit();
}

// general data appeared in account.php
$date = new DateTime();
$date->setTimestamp(intval($order['COMPLETE_TIME']));
$date->setTimezone(new DateTimeZone('Asia/Hong_Kong'));
$datestr = $date->format('Y/m/d H:i:s');
$escapedData = [$order['INVOICE_ID'], $datestr, $order['ORDER_STATUS'], $order['ITEM_COUNT'], 'HK$' . $order['TOTAL_PRICE']];
$escapedData = array_map(function($v) {return htmlspecialchars(strval($v));}, $escapedData);

$zipped = array_map(null, ['Invoice ID', 'Date', 'Status', 'Item Count', 'Total Price'], $escapedData);
?>

{% capture content %}
<section class="mx-auto text-center my-4">
	<table class="mx-auto">
        <?php
        foreach ($zipped as $i) {
            echo "<tr>
            <td class=\"text-end px-2\">$i[0]: </td>
            <td class=\"text-start px-2\">$i[1]</td>
            </tr>";
        }
        ?>
    </table>
</section>

<section class="mx-auto text-center mb-4">
	<table class="mx-auto">
        <tr>
            <th class="px-2">Name</th>
            <th class="px-2">Price</th>
            <th class="px-2">Quantity</th>
            <th class="px-2">Subtotal</th>
        </tr>
        <?php
        foreach (json_decode($order['ITEMS']) as $prod) {
            $escapedName = htmlspecialchars($prod->name);
            $price = floatval($prod->price);
            $quantity = intval($prod->quantity);
            $subtotal = $price * $quantity;
            echo "<tr>
            <td class=\"px-2\">$escapedName</td>
            <td class=\"px-2\">HK\$$price</td>
            <td class=\"px-2\">$quantity</td>
            <td class=\"px-2\">HK\$$subtotal</td>
            </tr>";
        }
        ?>
    </table>
</section>
{% endcapture %}

{% include_relative _layouts/default.html %}
