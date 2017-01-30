<?php

use game\SlotGame;

// Test module
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json;charset=utf-8");

/**
 * Request rpc class and functions
 */
require_once 'include/VcashRpc.php';
require_once 'include/Functions.php';

// microtime(true) returns the unix timestamp plus milliseconds as a float
$starttime = microtime(true);
/* do stuff here */

//echo "rpc_getinfo\n";
//echo json_encode(VcashRpc::rpc_getinfo(), JSON_PRETTY_PRINT);
//echo "\n\nrpc_getbalance\n";
//echo json_encode(VcashRpc::rpc_getbalance(), JSON_PRETTY_PRINT);
//echo "\n\nrpc_getblockcount\n";
//echo json_encode(VcashRpc::rpc_getblockcount(), JSON_PRETTY_PRINT);

$the_game = new SlotGame();

$total = 0;
$bets = 0;

for ($x = 1; $x <= 1000; $x++) {
    $slot_values = $the_game->launchFruitSlot();
    $score = $the_game->getComboScore($slot_values);
    $bets --;
    $total = $total - 1 + $score;
//    usleep(25000); // sleeps for 0.025 seconds

    echo json_encode($slot_values);
    echo " $x * $score * $total \n";
}

$ratio = round(-1 * $total/$bets, 3) * 100;
echo "\n $bets * $total * $ratio% \n";
$endtime = microtime(true);
$timediff = $endtime - $starttime;

//echo json_encode($timediff);

