---
---
<?php
session_start();
include_once('lib/auth.inc.php');

// if not login, redirect to login page
if (!($auth_info = auth())) {
    header('Location: login.php', true, 302);
	exit();
}
?>

{% capture content %}
<section class="container">
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
</section>

{% endcapture %}

{% include_relative _layouts/default.html %}