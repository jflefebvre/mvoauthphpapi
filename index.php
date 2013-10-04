<?php

include "vendor/autoload.php";

use Evolution\MobileVikings\MobileVikings;

$consumerKey = '';
$consumerSecret = '';
$userName = '';
$userPassword = '';
$msisdn = ''; // mobile number

$mv = new MobileVikings($consumerKey, $consumerSecret);


$response = $mv->fetch_access_token_via_xauth($userName, $userPassword);

// Methods available
// 

$response = $mv->get_msisdn_list();
// $response = $mv->get_price_plan_details($msisdn);
// $response = $mv->get_sim_balance($msisdn);
// $response = $mv->get_history($msisdn);
// $response = $mv->get_usage($msisdn);
// $response = $mv->get_sim_info($msisdn);
// $response = $mv->get_stats($msisdn);
// $response = $mv->get_links(); 
// $response = $mv->get_referrals();

var_dump(json_decode($response));
