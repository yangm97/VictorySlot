<?php

require_once 'Config.php';

// Set default timezone
date_default_timezone_set('UTC');



abstract class BetStatus
{
    const UNKNOWN = 0;
    const INITED = 1;
    const RECEIVED = 2;
    const LOCKED = 3;
    const CLOSED = 4;
}

class VcashDb
{
    // Warning: Open connection and create it in this script only, do not touch db connection outside, BAKA!
    public static function connectDb()
    {
        try {
            /**************************************
             * Create database and                 *
             * open connection                     *
             **************************************/
            $vcashdb = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USERNAME, DB_PASSWORD);
            $vcashdb->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $vcashdb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            // Set errormode to exceptions
            $vcashdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $vcashdb;
        } catch(PDOException $e) {
            // Print PDOException message
            die($e->getMessage());
        }
    }

/**************************************
 * Create tables                      *
 **************************************/
/*
CREATE TABLE IF NOT EXISTS bet (
id INTEGER AUTO_INCREMENT PRIMARY KEY,
house_address CHAR(50) NOT NULL,
user_address CHAR(50),
amount DOUBLE,
created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
status INTEGER DEFAULT 0,
score INTEGER DEFAULT 0,
reward DOUBLE DEFAULT 0,
refund DOUBLE DEFAULT 0,
game INTEGER DEFAULT 0,
currency INTEGER DEFAULT 0,
details TEXT,
INDEX(house_address)) ENGINE=InnoDB;
*/

    // Insert new bet (generated new address)
    public static function doInsertBet($db, $payload)
    {
        try {
            $status = BetStatus::INITED;
            $query = "INSERT INTO bet (house_address, status) VALUES (:house_address, :status);";
            $sth = $db->prepare($query);
            $sth->bindParam(":house_address", $payload['house_address']);
            $sth->bindParam(":status", $status);
            $sth->execute();
        }
        catch(PDOException $e) {
            $errorMsg = $e->getMessage();
            die($errorMsg);
        }
    }

    // Insert new bet (generated new address)
    public static function doUpdateBet($db, $payload)
    {
        try {
            $status = BetStatus::RECEIVED;
            $where_status = BetStatus::INITED;
            // Update the row with the house_address and if status is Inited
            $query = "UPDATE bet SET user_address = :user_address, amount = :amount, "
                    ."status = :status, score = :score, reward = :reward, refund = :refund, details = :details "
                    ."WHERE house_address = :house_address and status = :where_status;";
            $sth = $db->prepare($query);
            $sth->bindParam(":house_address", $payload['house_address']);
            $sth->bindParam(":user_address", $payload['user_address']);
            $sth->bindParam(":amount", $payload['amount']);
            $sth->bindParam(":status", $status);
            $sth->bindParam(":score", $payload['score']);
            $sth->bindParam(":reward", $payload['reward']);
            $sth->bindParam(":refund", $payload['refund']);
            $sth->bindParam(":details", $payload['details']); // add details
            $sth->bindParam(":where_status", $where_status);
            $retval = $sth->execute();
        }
        catch(PDOException $e) {
            $errorMsg = $e->getMessage();
            die($errorMsg);
        }
    }


    // Insert new bet (generated new address)
    public static function setBetLock($db, $payload)
    {
        try {
            $status = BetStatus::LOCKED;
            $where_status = BetStatus::RECEIVED;
            // Lock record for payment transaction, lock should be removed ans status closed
            // Only BetStatus::Received record could be locked
            // If the lock couldn't be set, stop. Another scritp put lock first
            $query = "UPDATE bet SET status = :status WHERE house_address = :house_address and status = :where_status;";
            $sth = $db->prepare($query);
            $sth->bindParam(":house_address", $payload['house_address']);
            $sth->bindParam(":status", $status);
            $sth->bindParam(":where_status", $where_status);
            $retval = $sth->execute();
        }
        catch(PDOException $e) {
            $errorMsg = $e->getMessage();
            die($errorMsg);
        }
    }


    // Remove status lock to restore received status
    // In case funds couldn't have been sent to the winner
    public static function removeBetLock($db, $payload)
    {
        try {
            $status = BetStatus::RECEIVED;
            $where_status = BetStatus::LOCKED;
            // Lock record for payment transaction, lock should be removed ans status closed
            // Only BetStatus::Received record could be locked
            // If the lock couldn't be set, stop. Another scritp put lock first
            $query = "UPDATE bet SET status = :status WHERE house_address = :house_address and status = :where_status;";
            $sth = $db->prepare($query);
            $sth->bindParam(":house_address", $payload['house_address']);
            $sth->bindParam(":status", $status);
            $sth->bindParam(":where_status", $where_status);
            $retval = $sth->execute();
        }
        catch(PDOException $e) {
            $errorMsg = $e->getMessage();
            die($errorMsg);
        }
    }


    // Insert new bet (generated new address)
    public static function doCloseBet($db, $payload)
    {
        try {
            $status = BetStatus::CLOSED;
            $received_status = BetStatus::RECEIVED;
            $locked_status = BetStatus::LOCKED;
            // Close a bet, if a previous payment attempt fails, do not close.
            $query = "UPDATE bet SET status = :status WHERE house_address = :house_address "
                    ."AND status IN (:received_status, :locked_status);";
            $sth = $db->prepare($query);
            $sth->bindParam(":house_address", $payload['house_address']);
            $sth->bindParam(":status", $status);
            $sth->bindParam(":received_status", $received_status);
            $sth->bindParam(":locked_status", $locked_status);
            $retval = $sth->execute();
        }
        catch(PDOException $e) {
            $errorMsg = $e->getMessage();
            die($errorMsg);
        }
    }


    // Get bet information from db
    public static function getBetInfo($db, $payload)
    {
        try {
            $query = "SELECT id, house_address, user_address, amount, created, updated, "
                    ."status, score, reward, refund, details "
                    ."FROM bet WHERE house_address = :house_address;";
            $sth = $db->prepare($query);
            $sth->bindParam(":house_address", $payload['house_address']);
            $sth->execute();
            $row = $sth->fetch(PDO::FETCH_ASSOC);
            $data = $row;
            return $data;
        }
        catch(PDOException $e) {
            $errorMsg = $e->getMessage();
            die($errorMsg);
        }
    }

    // Get bet status from db
    public static function getBetStatus($db, $payload)
    {
        try {
            $query = "SELECT id, house_address, status "
                    ."FROM bet WHERE house_address = :house_address;";
            $sth = $db->prepare($query);
            $sth->bindParam(":house_address", $payload['house_address']);
            $sth->execute();
            $row = $sth->fetch(PDO::FETCH_ASSOC);
            $data = $row;
            return $data;
        }
        catch(PDOException $e) {
            $errorMsg = $e->getMessage();
            die($errorMsg);
        }
    }
}