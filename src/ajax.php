<?php
// Ajax/json request module
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json;charset=utf-8");

/**
 * Request rpc class and functions
 */
require_once 'include/VcashRpc.php';
require_once 'include/Functions.php';
require_once 'include/BetDb.php';

$ajax_request = isAjaxCall();
if(!$ajax_request) {
    // Access allowed only for ajax requests
    echo json_encode(false);
    exit;
}

// Contrôle de validité de session
if(!isset($_SESSION['captcha']['code']) or !isset($_SESSION["checked"])) {
    // No valid session
    session_destroy();
    die("Error");
}

$requestType = "";

if(isset($_GET['cm'])) {
    $requestType = $_GET['cm'];

    //------------------------------
    // GET DATA
    //------------------------------
    // Determiner le type de requête
    if (strcasecmp($requestType, "getbalance") == 0) {
        echo json_encode(VcashRpc::rpc_getbalance());
        exit;
    }
    elseif (strcasecmp($requestType, "getnewaddress") == 0) {
        $db = VcashDb::connectDb();
        $payload = array();

        // Look  if a new house_address should be generated
        $get_newaddress = false;

        // -------- prepare $get_newaddress evaluation variable --------
        if (!isset($_SESSION["house_address"]) || empty($_SESSION["house_address"])) {
            // No previously generated address found
            $get_newaddress = true;
        }

        // Check in db if $_SESSION["house_address"] is still valid
        $payload['house_address'] = $_SESSION["house_address"];
        $req_data = VcashDb::getBetStatus($db, $payload);

        if(!isset($req_data['status']) || $req_data['status'] == BetStatus::CLOSED) {
            // $_SESSION["house_address"] is not found in db or is already closed
            $get_newaddress = true;
        }
        // -------- $get_newaddress is ready --------

        if ($get_newaddress) {
            // New address demand
            $response = VcashRpc::rpc_getnewaddress();
            $payload['house_address'] = $response['result'];
            // Insert new_adr in db to register possible bet
            VcashDb::doInsertBet($db, $payload);

            // Store last generated address for user in session!
            // If user comes back and has still a valid address in his session, use it, DO NOT REGENERATE A NEW ADDRESS!
            $_SESSION["house_address"] = $payload['house_address'];
        }
        else {
            // Generate the standart response with $_SESSION["house_address"]
            $response = array("id"=>1, "result"=>$_SESSION["house_address"]);
        }

        echo json_encode($response);
        exit;
    }
    elseif (strcasecmp($requestType, "test") == 0) {
        $result = playFruitSlot();
        echo json_encode($result);
        exit;
    }
    else{
        echo json_encode("Command not found");
        exit;
    }
}