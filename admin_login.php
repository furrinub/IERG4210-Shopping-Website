---
---
<?php
session_start();
$name = 's46';
$password = 'ierg4210';

// check login info
$wrong_pw = false;
if (isset($_POST["name"]) && isset($_POST["password"])) {
    if ($_POST["name"] == $name && $_POST["password"] == $password) {
        $_SESSION["isadmin"] = true;
        header('Location: admin.php');
        exit();
    } else {
        $wrong_pw = true;
    }
} elseif ($_GET["logout"] ?? false) {
    unset($_SESSION["isadmin"]);
    header('Location: index.php');
    exit();
}
?>

{% capture content %}
<section class="container">
    <fieldset class="my-4 text-center">
        <legend>Login to Continue</legend>
        <form id="login" method="POST" action="admin_login.php">
            <p>s46, ierg4210</p>
            <label for="user_name"> User Name:</label>
            <div> <input id="user_name" type="text" name="name" required="required" /></div>
            <label for="password"> Password:</label>
            <div> <input id="password" type="password" name="password" required="required" /></div>
            <input type="submit" value="Log in"/>
            <?php if ($wrong_pw) echo "<p class=\"text-danger\">Wrong name or password.</p>" ?>
        </form>
    </fieldset>
</section>

{% endcapture %}

{% include_relative _layouts/default.html %}