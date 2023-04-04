<?php
include_once('lib/db.inc.php');
$pwd_regex_option = array("options" => array("regexp" => "/^[\w@#$%\^\&\*\-]+$/"));

/*
Top Principle: Inputs need filter (sanitization + validation). Outputs need sanitization.
*/

function ierg4210_login() {
    global $pwd_regex_option;
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $pwd = filter_input(INPUT_POST, 'password', FILTER_VALIDATE_REGEXP, $pwd_regex_option);
    if (!$email || !$pwd) {
        throw new Exception('invalid account info');
    }

    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare('SELECT * FROM accounts WHERE email = ?;');
    $q->execute(array($email));
    if (!($res = $q->fetch())) {
        // wrong email
        header('Location: login.php?error=1', true, 302);
        exit();
    }

    $salt = $res["SALT"];
    $admin_flag = $res["ADMIN"];
    $db_hash = $res["PASSWORD"];
    $input_hash = hash_hmac('sha256', $pwd , $salt);

    // hash_equals - constant time comparison
    if (hash_equals($db_hash, $input_hash)) {
        // if correct password, set cookie and session
        $expire = time() + 3600 * 24 * 3; // 3 days
        $token = array(
            'email' => $res['EMAIL'],
            'expire' => $expire,
            'k' => hash_hmac('sha256', $expire . $db_hash, $salt),
            'admin' => $admin_flag
        );

        setcookie('auth', json_encode($token), $expire, '/', '/', true, true); // https only
        $_SESSION['auth'] = $token;
        session_regenerate_id();

        // admin account
        if ($admin_flag) {
            header('Location: admin.php', true, 302);
        } else {
            header('Location: index.php', true, 302);
        }
        return true;
    }

    // wrong pw
    header('Location: login.php?error=1', true, 302);
}

function ierg4210_logout() {
    // clear the cookies and session
    setcookie('auth', '', time() - 3600, '/', "/", true, true);
    session_destroy();

    header('Location: index.php', true, 302);
    exit();
}

function ierg4210_change_password() {
    global $pwd_regex_option;
    // havent login
    if (!($auth_info = auth())) {
        header('Location: login.php', true, 302);
        exit();
    }
    $email = $auth_info[0];
    $old_pwd = filter_input(INPUT_POST, 'old_password', FILTER_VALIDATE_REGEXP, $pwd_regex_option);
    $new_pwd_1 = filter_input(INPUT_POST, 'new_password_1', FILTER_VALIDATE_REGEXP, $pwd_regex_option);
    $new_pwd_2 = filter_input(INPUT_POST, 'new_password_2', FILTER_VALIDATE_REGEXP, $pwd_regex_option);
    if (!$email || !$old_pwd || !$new_pwd_1 || !$new_pwd_2) {
        throw new Exception('invalid account info');
    }
    $db = ierg4210_DB();
    $q = $db->prepare('SELECT * FROM accounts WHERE email = ?;');
    $q->execute(array($email));
    if (!($res = $q->fetch())) {
        // wrong email
        throw new Exception('invalid account info');
    }

    $salt = $res["SALT"];
    $db_hash = $res["PASSWORD"]; // hash
    $old_pw_hash = hash_hmac('sha256', $old_pwd , $salt);

    // hash_equals - constant time comparison
    if (!hash_equals($db_hash, $old_pw_hash)) {
        // wrong old pw
        header('Location: change_password.php?wrong_pw=1', true, 302);
        exit();
    }
    // check if confirm pw is the same. do not need to hash becuz both are user input
    if ($new_pwd_1 != $new_pwd_2) {
        // confirm pw not the same
        header('Location: change_password.php?wrong_confirm_pw=1', true, 302);
        exit();
    }
    // check if old and new pw is the same. do not need to hash becuz both are user input
    if ($old_pwd === $new_pwd_1) {
        // pw not change
        header('Location: change_password.php?same_pw=1', true, 302);
        exit();
    }

    // create new salt and pw hash
    $new_salt = gen_salt();
    $new_pwd_hash = hash_hmac('sha256', $new_pwd_1 , $new_salt);

    $q = $db->prepare('UPDATE accounts SET password = ?, salt = ? WHERE email = ?;');
    $q->execute(array($new_pwd_hash, $new_salt, $email));

    // logout after changing pw
    ierg4210_logout();
}

// do not check many things, unsafe
function create_account_internal($email, $pwd, $admin) {
    global $db;
    $db = ierg4210_DB();
    
    $q = $db->prepare('SELECT * FROM accounts WHERE email = ?;');
    $q->execute(array($email));
    if ($q->fetch()) {
        // email already exists
        throw new Exception('user already exists');
    }

    $salt = gen_salt();
    $pwd_hash = hash_hmac('sha256', $pwd, $salt);

    $q = $db->prepare("INSERT INTO (EMAIL, PASSWORD, SALT, ADMIN) accounts VALUES (?, ?, ?, ?);");
    $q->execute(array($email, $pwd_hash, $salt, $admin));
}

function create_account() {
    global $pwd_regex_option;
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $new_pwd_1 = filter_input(INPUT_POST, 'password_1', FILTER_VALIDATE_REGEXP, $pwd_regex_option);
    $new_pwd_2 = filter_input(INPUT_POST, 'password_2', FILTER_VALIDATE_REGEXP, $pwd_regex_option);

    if (!$email || !$new_pwd_1 || !$new_pwd_2) {
        throw new Exception('invalid account info');
    }

    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare('SELECT * FROM accounts WHERE email = ?;');
    $q->execute(array($email));
    if ($q->fetch()) {
        // email already exists
        throw new Exception('user already exists');
    }

    // TODO !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
}

// return [email (username), admin_flag] if it can log in
// return false otherwise
function auth() {
    if (!empty($_SESSION['auth'])) {
        return array($_SESSION['auth']['email'], $_SESSION['auth']['admin']);
    }
    if (!empty($_COOKIE['auth']) && $t = json_encode(stripslashes($_COOKIE['auth']), true)) {
        // dont trust user input (cookie). validate first.
        if (time() > $t['expire'] || !filter_var($t['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        global $db;
        $db = ierg4210_DB();
        $q = $db->prepare('SELECT * FROM accounts WHERE email = ?;');
        $q->execute(array($t['email']));
        if ($res = $q->fetch()) {
            $realk = hash_hmac('sha256', $t['expire'] . $res['PASSWORD'], $res['SALT']);
            if ($realk === $t['k']){
                $_SESSION['auth'] = $t;
                // dont use $t['admin']. Otherwise normal user can change the cookie to gain admin permission
                return array($res['EMAIL'], $res['ADMIN']);
            }
        }
    }
    return false;
}

function csrf_getNonce($action) {
    // random_int - cryptographically secure, uniformly selected integer
    $nonce = bin2hex(random_bytes(128)); // 128 bytes to hex = 256 chars
    if (!isset($_SESSION['csrf_nonce']))
        $_SESSION['csrf_nonce'] = array();
    $_SESSION['csrf_nonce'][$action] = $nonce;
    return $nonce;
}

function csrf_verifyNonce($action, $receive) {
    if (isset($receive) && ($_SESSION['csrf_nonce'][$action] ?? null) === $receive) {
        unset($_SESSION['csrf_nonce'][$action]);
        return true;
    }
    return false;
}

function gen_salt() {
    $salt = '';
    for ($i = 0; $i < 16; $i++) {
        $salt .= random_int(0, 9);
    }
    return $salt;
}
