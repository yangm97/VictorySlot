<?php
require_once 'include/random_compat/lib/random.php';

// Compare random generation functions mt_rand and random_int
$rounds = 1000;
if (isset($_GET['rnd'])) {
    $rounds = $_GET['rnd'];
    // Max rounds
    if ($rounds > 1000000) {
        $rounds = 1000000;
    }
}

$starttime = microtime(true);
$res = array_fill(0,10,0);
for ($i = 0; $i < $rounds; ++$i) {
    $res[mt_rand(0,9)]++;
}
var_dump($res);

$endtime = microtime(true);
$timediff = $endtime - $starttime;
echo json_encode($timediff);

var_dump("$$$");

$starttime = microtime(true);
$res = array_fill(0,10,0);
for ($i = 0; $i < $rounds; ++$i) {
    $res[random_int(0,9)]++;
}
var_dump($res);

$endtime = microtime(true);
$timediff = $endtime - $starttime;
echo json_encode($timediff);


