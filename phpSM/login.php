<?php
require 'classes/openid.class.php';

//session_start();
//session_destroy();
session_start();

try {
    $openid = new LightOpenID('http://89.133.153.164/phpSM/login.php');
    if(!$openid->mode) {
        $openid->identity = 'https://eu.wargaming.net/id/';
        $openid->required = array('namePerson/friendly');
        header('Location: ' . $openid->authUrl());
    } elseif($openid->mode == 'cancel') {
        echo 'User has canceled authentication!';
    } else {
        if ($openid->validate()) {
            $_SESSION["username"] = $openid->getAttributes()['namePerson/friendly'];
            setSessionParameters();
            header('Location: ' . 'index.php');
        } else {
            echo ('Authentication failed!');
            unset($_SESSION["username"]);
        }
    }
} catch(ErrorException $e) {
    echo $e->getMessage();
}

function setSessionParameters() {
    $valid = false;
    
    $vteps = json_decode(getClanDetailsJSON("500002053"), true);
    $vt3ps = json_decode(getClanDetailsJSON("500025525"), true);

    if (isset($vteps["data"][500002053]["members"])) {
        foreach ($vteps["data"][500002053]["members"] as $member) {
            if ($member["account_name"] == $_SESSION["username"]) {
                $_SESSION["clan"] = "VTEPS";
                $_SESSION["role"] = $member["role"];
                $valid = true;
            }
        }
    }

    if (isset($vt3ps["data"][500025525]["members"])) {
        foreach ($vt3ps["data"][500025525]["members"] as $member) {
            if ($member["account_name"] == $_SESSION["username"]) {
                $_SESSION["clan"] = "VT3PS";
                $_SESSION["role"] = $member["role"];
                $valid = true;
            }
        }
    }
    
    $_SESSION["valid"] = $valid;
}

function getClanDetailsJSON($clanid) {
    $curl_handle=curl_init();
    curl_setopt($curl_handle, CURLOPT_URL,'http://api.worldoftanks.eu/2.0/clan/info//?application_id=d0a293dc77667c9328783d489c8cef73&clan_id=' . $clanid);
    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Clan Checker');
    $results = curl_exec($curl_handle);
    curl_close($curl_handle);
    
    return $results;
}
