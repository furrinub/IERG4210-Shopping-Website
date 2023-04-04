<?php
/*
https://blog.csdn.net/weixin_33953384/article/details/92013600
wasted many hours to debug!!!!!!!
need to give permission to apache for 
1. the folder of db
2. db file
3. the code (php files)
*/

function ierg4210_DB() {
	// connect to the database
	// TODO: change the following path if needed
	// Warning: NEVER put your db in a publicly accessible location
	$db = new PDO('sqlite:/var/www/cart.db');

	// enable foreign key support
	$db->query('PRAGMA foreign_keys = ON;');

	// FETCH_ASSOC:
	// Specifies that the fetch method shall return each row as an
	// array indexed by column name as returned in the corresponding
	// result set. If the result set contains multiple columns with
	// the same name, PDO::FETCH_ASSOC returns only a single value
	// per column name.
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // show warning message if any db errors occur
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

	return $db;
}

/* ===== DB Category Function ===== */

function ierg4210_cat_insert() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    // input validation or sanitization
    $name = $_POST["name"];
    if (!valid_text($name))
        throw new Exception("invalid-name");

    $sql = "INSERT INTO CATEGORIES (CID, NAME) VALUES (NULL, ?);";
    $q = $db->prepare($sql);
    $q->bindParam(1, $name);
    $q->execute();

    // redirect back to original page; you may comment it during debug
    header('Location: admin.php', true, 302);
    exit();
}

function ierg4210_cat_edit() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    // input validation or sanitization
    $cid = $_POST["cid"];
    $name = trim($_POST["name"]);
    if (!valid_int($cid))
        throw new Exception("invalid-cid");
    $cid = (int) $cid;
    if (!valid_text($name))
        throw new Exception("invalid-name");

    $sql = "UPDATE CATEGORIES SET NAME = ? WHERE CID = ?;";
    $q = $db->prepare($sql);
    $q->execute(array($name, $cid));

    // redirect back to original page; you may comment it during debug
    header('Location: admin.php', true, 302);
    exit();
}

function ierg4210_cat_delete() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    // input validation or sanitization
    $cid = $_POST["cid"];
    if (!valid_int($cid))
        throw new Exception("invalid-cid");
    $cid = (int) $cid;

    $sql = "DELETE FROM CATEGORIES WHERE CID = ?;";
    $q = $db->prepare($sql);
    $q->bindParam(1, $cid);
    $q->execute();

    // redirect back to original page; you may comment it during debug
    header('Location: admin.php', true, 302);
    exit();
}

function ierg4210_cat_fetchAll() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM categories;");
    if ($q->execute())
        return $q->fetchAll();
}

/* ===== DB Product Function ===== */

// Since this form will take file upload, we use the tranditional (simpler) rather than AJAX form submission.
// Therefore, after handling the request (DB insert and file copy), this function then redirects back to admin.html
function ierg4210_prod_insert() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    // input validation or sanitization
    $cid = $_POST["cid"];
    $name = $_POST["name"];
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];
    $desc = $_POST["description"];
    if (!valid_int($cid))
        throw new Exception("invalid-cid");
    if (!valid_text($name))
        throw new Exception("invalid-name");
    if (!valid_float($price))
        throw new Exception("invalid-price");
    if (!valid_float($quantity))
        throw new Exception("invalid-quantity");
    if (!valid_text($desc))
        throw new Exception("invalid-text");

    // Copy the uploaded file to a folder which can be publicly accessible at incl/img/[pid].jpg
    $mime_type = mime_content_type($_FILES["file"]["tmp_name"]);
    if ($_FILES["file"]["error"] === 0
        && $_FILES["file"]["type"] === $mime_type
        && ($mime_type === "image/jpeg"
            || $mime_type === "image/png"
            || $mime_type === "image/gif")
        && $_FILES["file"]["size"] <= 5*1024*1024) {

        $sql = "INSERT INTO PRODUCTS (CID, NAME, PRICE, QUANTITY, DESCRIPTION) VALUES (?, ?, ?, ?, ?);";
        $q = $db->prepare($sql);
        $q->execute(array($cid, $name, $price, $quantity, $desc));
        $lastId = $db->lastInsertId(); // == PID

        // save img
        create_image_and_thumbnail($_FILES["file"]["tmp_name"], $lastId);
        header('Location: admin.php', true, 302);
        exit();
    }

    // Only an invalid file will result in the execution below
    // To replace the content-type header which was json and output an error message
    header('Content-Type: text/html; charset=utf-8');
    echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}

function ierg4210_prod_edit() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    // input validation or sanitization
    $pid = $_POST["pid"];
    $cid = $_POST["cid"];
    $name = $_POST["name"];
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];
    $desc = $_POST["description"];
    
    if (!valid_int($pid))
        throw new Exception("invalid-pid");
    $pid = (int) $pid;
    if (!valid_int($cid))
        throw new Exception("invalid-cid");
    if (!valid_text($name))
        throw new Exception("invalid-name");
    if (!valid_float($price))
        throw new Exception("invalid-price");
    if (!valid_int($quantity))
        throw new Exception("invalid-quantity");
    if (!valid_text($desc))
        throw new Exception("invalid-desc");

    // Copy the uploaded file to a folder which can be publicly accessible at incl/img/[pid].jpg
    $mime_type = mime_content_type($_FILES["file"]["tmp_name"]);
    if ($_FILES["file"]["error"] === 0
        && $_FILES["file"]["type"] === $mime_type
        && ($mime_type === "image/jpeg"
            || $mime_type === "image/png"
            || $mime_type === "image/gif")
        && $_FILES["file"]["size"] <= 5*1024*1024) {

        // run sql
        $sql = "UPDATE PRODUCTS SET CID = ?, NAME = ?, PRICE = ?, QUANTITY = ?, DESCRIPTION = ? WHERE PID = ?;";
        $q = $db->prepare($sql);
        $q->execute(array($cid, $name, $price, $quantity, $desc, $pid));
        
        // save img
        create_image_and_thumbnail($_FILES["file"]["tmp_name"], $pid);
        header('Location: admin.php', true, 302);
        exit();
    }

    // Only an invalid file will result in the execution below
    // To replace the content-type header which was json and output an error message
    header('Content-Type: text/html; charset=utf-8');
    echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}

function ierg4210_prod_delete() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    // input validation or sanitization
    $pid = $_POST["pid"];
    if (!valid_int($pid))
        throw new Exception("invalid-pid");
    $pid = (int) $pid;

    // run sql
    $sql = "DELETE FROM PRODUCTS WHERE PID = ?;";
    $q = $db->prepare($sql);
    $q->bindParam(1, $pid);
    $q->execute();

    // redirect back to original page; you may comment it during debug
    header('Location: admin.php', true, 302);
    exit();
}

function ierg4210_prod_fetch_by_cid($cid=null) {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    $cid = $cid ?? $_GET["cid"] ?? 'x';
    if (!valid_int($cid))
        throw new Exception("invalid-pid");

    $sql = "SELECT * FROM PRODUCTS WHERE CID = ?;";
    $q = $db->prepare($sql);
    $q->bindParam(1, $cid);
    if ($q->execute())
        return $q->fetchAll();
}

function ierg4210_prod_fetchAll() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM PRODUCTS LIMIT 100;");
    if ($q->execute())
        return $q->fetchAll();
}

function ierg4210_prod_fetchOne($pid=null) {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    $pid = $pid ?? $_GET["pid"] ?? 'x';
    if (!valid_int($pid))
        throw new Exception("invalid-pid");

    $sql = "SELECT * FROM PRODUCTS WHERE PID = ?;";
    $q = $db->prepare($sql);
    $q->bindParam(1, $pid);
    if ($q->execute())
        return $q->fetch();
}

function ierg4210_prod_fetchThreeRandom() {
    $prod = ierg4210_prod_fetchAll();
    $random_index = count($prod) == 0 ? array() : array_rand($prod, min(count($prod), 4));

    $res = array();
    foreach ($random_index as $i) {
        array_push($res, $prod[$i]);
    }
    return $res;
}

/* ===== DB Order Function ===== */

function order_insert($email, $invoiceId, $completeTime, $status, $totalPrice, $itemCount, $itemJson) {
    global $db;
    $db = ierg4210_DB();

    if ($email !== 'guest' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("invalid-email");
    }
    if (!valid_uuidv4($invoiceId)) {
        throw new Exception("invalid-uuid");
    }
    if (!valid_int($completeTime)) {
        throw new Exception("invalid-time");
    }
    if (!valid_float($totalPrice)) {
        throw new Exception("invalid-price");
    }
    if (!valid_int($itemCount)) {
        throw new Exception("invalid-count");
    }
    if (!valid_json($itemJson)) {
        throw new Exception("invalid-json");
    }

    $q = $db->prepare("INSERT INTO orders (EMAIL, INVOICE_ID, COMPLETE_TIME, ORDER_STATUS, TOTAL_PRICE, ITEM_COUNT, ITEMS) VALUES (?, ?, ?, ?, ?, ?, ?);");
    return $q->execute(array($email, $invoiceId, $completeTime, $status, $totalPrice, $itemCount, $itemJson));
}

function order_fetchAll() {
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM orders ORDER BY COMPLETE_TIME ASC;");
    if ($q->execute())
        return $q->fetchAll();
}

function order_fetchOne($transcationId) {
    global $db;
    $db = ierg4210_DB();

    if (!valid_int($transcationId)) {
        throw new Exception("invalid transaction id");
    }

    $q = $db->prepare("SELECT * FROM orders WHERE TRANSACTION_ID = ?;");
    if ($q->execute(array($transcationId)))
        return $q->fetch();
}

function order_fetch_by_email($email) {
    global $db;
    $db = ierg4210_DB();

    if ($email !== 'guest' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("invalid-email");
    }

    $q = $db->prepare("SELECT * FROM orders WHERE EMAIL = ? ORDER BY COMPLETE_TIME ASC LIMIT 5;");
    if ($q->execute(array($email)))
        return $q->fetchAll();
}


// not really text
function valid_text($v) {
    return preg_match('/^[\w\s\-_!?.,()]+$/', $v);
}

// not really int
function valid_int($v) {
    return is_int($v) || preg_match('/^\d*$/', $v);
}

// not really float
function valid_float($v) {
    return preg_match('/^[\d\.]+$/', strval($v));
}

// https://www.geeksforgeeks.org/how-to-validate-json-in-php/
function valid_json($v) {
    return !empty($v) && is_string($v) && is_array(json_decode($v, true));
}

// https://stackoverflow.com/questions/12808597/php-verify-valid-uuid
function valid_uuidv4($v) {
    return is_string($v) && preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $v);
}

function create_image_and_thumbnail($file, $pid) {
    $img_path = "/var/www/html/product_images/";
    $thumbnail_path = $img_path . "thumbnails/";
    // delete old big picture and thumbnail because they may have different extensions
    foreach (glob($img_path . $pid . ".*") as $filename) {
        unlink($filename);
    }
    foreach (glob($thumbnail_path . $pid . ".*") as $filename) {
        unlink($filename);
    }

    // Resize image for thumbnail and big picture
    list($old_width, $old_height) = getimagesize($file);
    try {
        $src = imagecreatefromstring(file_get_contents($file));
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    // resize thumbnail
    $thumbnail_width = 250;
    $dst = imagecreatetruecolor($thumbnail_width, $thumbnail_width);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $thumbnail_width, $thumbnail_width, $old_width, $old_height);
    if (!imagewebp($dst, $thumbnail_path . $pid . '.webp'))
        throw new Exception("cant export webp");
    imagedestroy($dst);
    
    // resize big picture
    $img_width = 1200;
    $dst = imagecreatetruecolor($img_width, $img_width);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $img_width, $img_width, $old_width, $old_height);
    if (!imagewebp($dst, $img_path . $pid . '.webp'))
        throw new Exception("cant export webp");
    imagedestroy($dst);

    imagedestroy($src);
}

function find_name_by_cid($cats, $cid) {
	return $cats[((int) $cid) - 1]['NAME'];
}
