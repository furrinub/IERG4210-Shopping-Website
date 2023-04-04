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


{% endcapture %}

{% include_relative _layouts/default.html %}