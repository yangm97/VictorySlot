<?php
use game\SlotGame;

// Ajax/json request module
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json;charset=utf-8");


/**
 * Request rpc class and functions
 */
require_once 'include/VcashRpc.php';
require_once 'include/BetDb.php';
require_once 'include/Functions.php';

$ajax_request = isAjaxCall();
if(!$ajax_request) {
    // Access allowed only for ajax requests
    echo json_encode(null);
    exit;
}

// Contrôle de validité de session
if(!isset($_SESSION['captcha']['code']) or !isset($_SESSION["checked"])) {
    // No valid session
    session_destroy();
    die("Error");
}

$db = VcashDb::connectDb();

// Default response, status Unknown
// status, reward, score, details. That's all! Add a payment status - pending or completed?
$data_response = array("status"=> BetStatus::UNKNOWN, "reward"=>null, "score"=>null, "details"=>null);
$house_address = htmlspecialchars(isset($_GET['house_address']) ? $_GET['house_address'] : null);
// Check bet status in db if closed, stop
$req_data = VcashDb::getBetInfo($db, array('house_address'=>$house_address));

// Prepare response from db
// Just return informations from db to user, do not write/update data in db
$data_response["status"] = $req_data['status'];
// Set bet reward, winner, score position and other stuff
$data_response["reward"] = $req_data['reward'];
$data_response["score"] = $req_data['score'];
$data_response["details"] = json_decode($req_data['details']);

// Check statuses UNKNOWN, Received, Locked, Closed
if(!isset($req_data['status']) || $req_data['status'] == BetStatus::UNKNOWN) {
    // house_address not found in db
    cleanBetSession();

    echo json_encode($data_response);
    exit;
}
elseif($req_data['status'] == BetStatus::LOCKED || $req_data['status'] == BetStatus::CLOSED) {
    // We got a call for a closed bet or already locked (result set)

    // !!! Change session vars
    cleanBetSession();

    echo json_encode($data_response);
    exit;
}
elseif($req_data['status'] == BetStatus::RECEIVED) {
    // Somehow funds were not transfered and bet not closed, solve that
    $total_amount = $req_data['reward']+$req_data['refund'];
    // Transfer funds and close the bet
    $trans_status = transferFunds($db, $total_amount, $house_address, $req_data['user_address']);
    $data_response["status"] = $trans_status;

    // !!! Change session vars
    cleanBetSession();

    echo json_encode($data_response);
    exit;
}

// Bet found in db and it's status is at least BetStatus::Inited or BetStatus::Received
// Check transaction data and check in db bet status
$bet_data = VcashRpc::check_received($house_address);

// Bet found in db but no funds received
if ($bet_data['received'] == false) {
    // Not received yet
    $data_response["status"] = BetStatus::INITED;

    echo json_encode($data_response);
    exit;
}

$user_address = $bet_data['user_address'];
$amount = $bet_data['amount'];

// If the user sends more coins than MAX_AMOUNT, refund him
$amount_to_refund = 0;
if ($amount > MAX_AMOUNT) {
    if (REFUND_DEPOSIT)
        $amount_to_refund = $amount - MAX_AMOUNT;
    $amount = MAX_AMOUNT;
}

// Play game
$the_game = new SlotGame();
$result_slot = $the_game->playGame(); // returns array("values"=>$slot_values, "indexes"=>$indexes, "score"=>$score)
$score = $result_slot['score'];
// Calculate the reward
$amount_to_pay = $amount * $score;

// Set result in db
$bet_data['score'] = $score;
$bet_data['reward'] = $amount_to_pay;
$bet_data['refund'] = $amount_to_refund;
$bet_data['details'] = json_encode($result_slot); // Save details in db, encode json to string

VcashDb::doUpdateBet($db, $bet_data);

$total_amount = $amount_to_pay + $amount_to_refund;

// Prepare ajax final response
$data_response["reward"] = $amount_to_pay;
$data_response["score"] = $score;
$data_response["details"] = $result_slot;
$data_response["status"] = BetStatus::RECEIVED;

// Close the bet and Transfer $total_amount to $user_address if needed
$trans_status = transferFunds($db, $total_amount, $house_address, $user_address);
$data_response["status"] = $trans_status;

// !!! Change session vars
cleanBetSession();

echo json_encode($data_response);
exit;

