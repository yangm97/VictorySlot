<?php
/*
 * General use function
 *
 */

require_once 'Config.php';
require_once 'random_compat/lib/random.php';
// Include slots functions
require_once 'AbstractGame.php';
require_once 'SlotGame.php';

function isAjaxCall() {
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
    {
        //Request identified as ajax request
        return true;
    }
    return false;
}

function setDefaultBetSessionVars() {
    // !!! Change session vars
    $_SESSION["house_address"] = null;
    $_SESSION["funds"] = 0;
}

// Transfer $total_amount to $user_address
function transferFunds($db, $total_amount, $house_address, $user_address) {
    try {
        $bet_data = array("house_address"=>$house_address);

        if ($total_amount > 0) {
            // Check the funds available
            $rpc_balance = VcashRpc::rpc_getbalance();
            $tot_balance = $rpc_balance['result'];

            if ($tot_balance > $total_amount) {
                // Check if locked
                // Set the lock in db
                VcashDb::setBetLock($db, $bet_data); // Locked

                // Pay the user
                if (PAYOUT_ENABLED) {
                    // sendtoaddress $user_address $total_amount
                    $transact = VcashRpc::rpc_sendtoaddress($user_address, $total_amount);
                }
                else {
                    // Dummy response for test purposes
                    $transact = array("stuff"=>1);
                }

                // check errors: {"error":{"code":-4,"message":"insufficient funds"},"id":"1"}
                if (!is_null($transact) && !isset($transact['error'])) {
                    // Transaction success, close bet in db
                    VcashDb::doCloseBet($db, $bet_data); // Closed
                }
                else {
                    // Error transaction, remove lock in db
                    VcashDb::removeBetLock($db, $bet_data);
                }
            }
        }
        else {
            // No funds to transfert, just close that bet!
            VcashDb::doCloseBet($db, $bet_data); // Closed
        }

        // Fetch the statut from db
        $req_db = VcashDb::getBetStatus($db, $bet_data);

        return $req_db['status'];

    } catch (Exception $e) {
        die($e->getMessage());
    }
}

