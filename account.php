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
?>

{% capture content %}
<section class="row mx-auto text-center">
	<div class="col-3 px-0 admin-nav">
		<ul class="nav flex-column">
			<li class="nav-item"><a href="account.php?page=order" class="nav-link text-black py-3">Orders</a></li>
			<li class="nav-item"><a href="account.php?page=change_password" class="nav-link text-black py-3">Change Password</a></li>
		</ul>
	</div>
	<div class="col px-4">
		<?php
		$dstpage = $_GET["page"] ?? "order";
		if ($dstpage === "change_password") {
		?>
			<div class="container">
				<fieldset class="my-4 text-center">
					<legend>Change password</legend>
					<form id="change_password" method="POST" action="login_process.php?action=change_password">
						<label for="password"> Old Password:</label>
						<div> <input id="old_password" type="password" name="old_password" required="required" /></div>
						<label for="password"> New Password:</label>
						<div> <input id="new_password_1" type="password" name="new_password_1" required="required" /></div>
						<label for="password"> Confirm New Password:</label>
						<div> <input id="new_password_2" type="password" name="new_password_2" required="required" /></div>
						<input type="submit" value="Change"/>
						<input type="hidden" name="nonce" value="<?= csrf_getNonce('change_password') ?>"/>
						<?php if (isset($_GET['wrong_pw'])) echo "<p class=\"text-danger\">Wrong old password.</p>" ?>
						<p id="wrong_confirm_alert" class="text-danger <?php if (!isset($_GET['wrong_confirm_pw'])) echo 'd-none'?>">Wrong confirm password.</p>
						<p id="same_alert" class="text-danger <?php if (!isset($_GET['same_pw'])) echo 'd-none'?>">Old and new password cannot be the same.</p>
					</form>
				</fieldset>
			</div>
			<script src="js/change_password.js"></script>
		<?php
		} else {
		?>
			<h3 class="my-4 text-start">Orders</h3>
			<table class="db-table mb-4">
				<tr>
					<th>Invoice ID</th>
					<th>Date</th>
					<th>Status</th>
					<th>Item Count</th>
                    <th>Total Price</th>
                    <th>Details</th>
				</tr>
				<?php
				$email = $_SESSION['auth']['email'];
                $orders = order_fetch_by_email($email);
				foreach ($orders as $order) {
					$date = new DateTime();
					$date->setTimestamp(intval($order['COMPLETE_TIME']));
					$date->setTimezone(new DateTimeZone('Asia/Hong_Kong'));
					$datestr = $date->format('Y/m/d H:i:s');

					$escapedData = [$order['INVOICE_ID'], $datestr, $order['ORDER_STATUS'], $order['ITEM_COUNT'], $order['TOTAL_PRICE']];
					$escapedData = array_map(function($v) {return htmlspecialchars(strval($v));}, $escapedData);
					$escapedTransactionId = urlencode($order['TRANSACTION_ID']);
					echo "<tr>
					<td>$escapedData[0]</td>
					<td>$escapedData[1]</td>
					<td>$escapedData[2]</td>
					<td>$escapedData[3]</td>
					<td>$escapedData[4]</td>
					<td><a href=\"order_details.php?id=$escapedTransactionId\">More Details</a></td>
					</tr>";
				}
				?>
			</table>
		<?php
		}
		?>
	</div>
</section>
{% endcapture %}

{% include_relative _layouts/default.html %}
