<?php
include_once('lib/db.inc.php');
include_once('lib/auth.inc.php');

header('Content-Type: application/json');

// input validation
if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action'])) {
	echo json_encode(array('failed'=>'undefined'));
	exit();
}

// all fetch action does not need nonce and auth. The actions do not edit anything. Otherwise js cant fetch info to be displayed in index.php.
$fetch_action_type = array('prod_fetchThreeRandom', 'prod_fetch_by_cid', 'prod_fetchAll', 'prod_fetchOne', 'cat_fetchAll');
if (!in_array($_REQUEST['action'], $fetch_action_type) && !csrf_verifyNonce($_REQUEST['action'], $_POST['nonce'])) {
    header('Location: admin.php', true, 302);
    exit();
}

if (!in_array($_REQUEST['action'], $fetch_action_type)) {
	// if havent log in
	if (!($auth_info = auth())) {
		header('Location: login.php', true, 302);
		exit();
	}
	// if user is not admin
	if (!$auth_info[1]) {
		header('Location: index.php', true, 302);
		exit();
	}
}

// The following calls the appropriate function based to the request parameter $_REQUEST['action'],
//   (e.g. When $_REQUEST['action'] is 'cat_insert', the function ierg4210_cat_insert() is called)
// the return values of the functions are then encoded in JSON format and used as output
try {
	if (($returnVal = call_user_func('ierg4210_' . $_REQUEST['action'])) === false) {
		if ($db && $db->errorCode()) 
			error_log(print_r($db->errorInfo(), true));
		echo json_encode(array('failed'=>'1'));
	}
	echo 'while(1);' . json_encode(array('success' => $returnVal));
} catch(PDOException $e) {
	error_log($e->getMessage());
	echo json_encode(array('failed'=>'error-db'));
} catch(Exception $e) {
	echo 'while(1);' . json_encode(array('failed' => $e->getMessage()));
}
?>