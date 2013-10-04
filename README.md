mvoauthphpapi
=============

Mobile vikings API php client PSR0

Add the following lines in your composer.json. (
```
    "repositories": [
      {
            "url": "https://github.com/jflefebvre/mvoauthphpapi.git",
            "type": "git"
        }
    ],
    "require": {
	    "jflefebvre/mvoauthphpapi":"dev-master"		
	}
```

You must contact the helpdesk () to ask to activate xauth.
Check you oauth settings in your account settings.

The use of the library is pretty straightforward :

````
<?php

include "vendor/autoload.php";

use Evolution\MobileVikings\MobileVikings;

$consumerKey = '';
$consumerSecret = '';
$userName = '';
$userPassword = '';
$msisdn = ''; // mobile number associated (you can have several)

$mv = new MobileVikings($consumerKey, $consumerSecret);


$response = $mv->fetch_access_token_via_xauth($userName, $userPassword);

// $response = $mv->get_msisdn_list();

// Methods available
// 
// $response = $mv->get_price_plan_details($msisdn);
$response = $mv->get_sim_balance($msisdn);
// $response = $mv->get_history($msisdn);
// $response = $mv->get_usage($msisdn);
// $response = $mv->get_sim_info($msisdn);
// $response = $mv->get_stats($msisdn);
// $response = $mv->get_links(); 
// $response = $mv->get_referrals();

var_dump(json_decode($response));

```

[Mobile Vikings API documentation](https://mobilevikings.com/api/2.0/doc/)
