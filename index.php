<?php

include 'EvoCurl.php';
include 'EvoOAuth.php';
include 'EvoMobileVikings.php';

/*
 *  Class to integrate with Mobile Vikings API.
 *  Authenticated calls are done using xAuth and require access tokens for a user.
 * 
 *  Full documentation available on github
 *  https://github.com/jflefebvre/mvoauthphpapi
 *
 *  OAuth settings : https://mobilevikings.com/fr/account/edit/oauth-credentials/ 
 * 
 *  Ask to enable xauth for your account via e-mail : info@mobilevikings.com
 *  
 *  @author Jean-François Lefebvre <lefebvre.jf@gmail.com>
 */

// OAuth settings
$consumerKey = '';      
$consumerSecret = '';   

// Mobile vikings username/password
$userName = '';        
$userPassword = '';    

$msisdn = ''; // phone number format example +32494688701

$mv = new EvoMobileVikings($consumerKey, $consumerSecret);

$response = $mv->fetch_access_token_via_xauth($userName, $userPassword);

$response = $mv->get_msisdn_list();

// Methods available
// 
// $response = $mv->get_price_plan_details($msisdn);
// $response = $mv->get_sim_balance($msisdn);
// $response = $mv->get_history($msisdn);
// $response = $mv->get_usage($msisdn);
// $response = $mv->get_sim_info($msisdn);
// $response = $mv->get_stats($msisdn);
// $response = $mv->get_links(); 
// $response = $mv->get_referrals();

var_dump(json_decode($response));
