<?php
session_start();

require_once('src/BigInteger.php');
require_once('src/Srp6.class.php');



$action = $_POST["action"];

switch ($action) {
    case 'Initial':
        handleInitial($_POST["A"], $_POST["username"]);
        break;
    case 'Verification':
        handleVerification($_POST["verificationHash"]);
        break;
    
    default:
        handleDefault($action);
        break;
}


function handleInitial($A, $username)
{
    $srp = new Srp6();

    // Get the v for Carol
    $v = getVFromDatabase($username);
    $srp->setV($v);

    $srp->calculateBandU($A);

    // to send
    $salt = getSaltFromDb($username);
    $B = $srp->getB();
    $u = $srp->getU();


    // to delete
    $srp->computeS($A);

    $ret = array(
        'status' => 'ok', 
        'salt' => $salt, 
        'B' => $B, 
        'u' => $u,
        'A' => $A,
        'serverKey' => $srp->getKey());

    $_SESSION["srp"] = serialize($srp);
    die(json_encode($ret));
}

function handleVerification($clientHash)
{
    $srp = unserialize($_SESSION["srp"]);

    $serverHash = $srp->generateServerHash();

    if ($serverHash == $clientHash) {
        echo (json_encode(array('status' => 'ok')));
    } else {
        echo (json_encode(array('status' => 'fail')));
    }

}

function handleDefault($action)
{
    die("Sorry, action: '$action' has not been implemented.");
}

function getVFromDatabase($username)
{
    // Generated by
    // $x = hash("sha256", "carols-salt" . "carols-password");
    // $v = $srp->generateV($x);

    // SELECT v from users where username = $username
    return "be2f67918a0d6931b35f676ef722f3d9f7d78169c3ff1c9654c700a5677259a3754f832926f1983eff4fcd63060d65ed3ad8fc094b9a38597c9f77af2d334ec20467fd07891603b5e4536ba37ed55955d97836c77b51de2e70411e17f749c5468ed378699e48af779ea9e4df0df23865274d7239f3dfafec421c94477a86878b";
}

function getSaltFromDb($username)
{
    // SELET salt from users where username = $username
    return "carols-salt";
}

/*
 * Take the modulus of $number
 */
function bigMod($number, $mod)
{
    return $number->modPow(new Math_BigInteger(1), $mod);
}
function hexToBigInt($string)
{
    // Remove : and all whitespace
    $string = str_replace(":","", $string);
    $string = preg_replace('/\s+/', '', $string);
    return new Math_BigInteger('0x' . $string, 16); 
}

?>