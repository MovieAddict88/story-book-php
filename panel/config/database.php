<?php
define('DB_HOST', 'sql311.infinityfree.com');
define('DB_USER', 'if0_40043611');
define('DB_PASS', '4VVob4pFy2oKqTx');
define('DB_NAME', 'if0_40043611_netflix');

function connect() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>