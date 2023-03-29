---
---
<?php
session_start();
include_once('lib/auth.inc.php');

// if already login, redirect to other pages
if ($auth_info = auth()) {
    header('Location: index.php', true, 302);
	exit();
}
?>

{% capture content %}
<section class="container">
    <fieldset class="my-4 text-center">
        <legend>Login to Continue</legend>
        <form id="login" method="POST" action="login_process.php?action=login">
            <label for="email"> Email:</label>
            <div> <input id="email" type="email" name="email" required="required" /></div>
            <label for="password"> Password:</label>
            <div> <input id="password" type="password" name="password" required="required" /></div>
            <input type="submit" value="Log in"/>
            <input type="hidden" name="nonce" value="<?= csrf_getNonce('login') ?>"/>
            <?php if (isset($_GET['error'])) echo "<p class=\"text-danger\">Wrong email or password.</p>" ?>
        </form>
    </fieldset>
</section>


{% endcapture %}

{% include_relative _layouts/default.html %}