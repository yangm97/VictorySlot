<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json;charset=utf-8");

/**
 * Request functions
 */
require_once 'include/Functions.php';

$ajax_request = isAjaxCall();
if(!$ajax_request) {
    // Access allowed only for ajax requests
    echo json_encode(false);
    exit;
}

$data = array();
// Contrôle de validité de session
if(!isset($_SESSION['captcha']['code'])) {
    // No valid session
    session_destroy();
    die("Error");
}

if (isset($_SESSION['checked']) && $_SESSION["checked"]) {
    // Already checked, ok
    echo json_encode(true);
    exit;
}

if (isset($_POST['captcha_code'])) {
    //------------------------------
    // COMPARE CAPTCHA
    //------------------------------
    $requestCaptcha = $_POST['captcha_code'];
    // CHECK DISABLED FOR TESTS
    if (strcasecmp(strtolower($requestCaptcha), strtolower($_SESSION['captcha']['code'])) == 0) {
        $_SESSION["checked"]=true;
        echo json_encode(true);
        exit;
    }
}
// Default false
echo json_encode(false);
exit;