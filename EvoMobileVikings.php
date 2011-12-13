<?php
/*
 *  Class to integrate with Mobile Vikings API.
 *  Authenticated calls are done using xAuth and require access tokens for a user.
 * 
 *  Full documentation available on github
 *  https://github.com/jflefebvre/mvoauthphpapi
 * 
 *  @author Jean-François Lefebvre <lefebvre.jf@gmail.com>
 */
class EvoMobileVikings extends EvoOAuth
{
  const EVOMOBILEVIKINGS_SIGNATURE_METHOD = 'HMAC-SHA1';
  const EVOMOBILEVIKINGS_AUTH_OAUTH = 'oauth';
  const EVOMOBILEVIKINGS_AUTH_BASIC = 'basic';
  
  protected $requestTokenUrl= '/request_token/';
  protected $accessTokenUrl = '/access_token/';
  protected $authorizeUrl   = '/authorize/';
  
  protected $apiUrl         = 'https://mobilevikings.com:443';
  protected $apiVersionedUrl= 'https://mobilevikings.com:443';
  protected $userAgent      = 'EvoMobileVikings (http://)';
  protected $apiVersion     = '2.0';
  protected $isAsynchronous = false;
  
  /**
   * fetch_access_token_via_xauth
   * 
   * 
   * @param string $userName
   * @param string $userPassword
   * 
   * @return EvoMobileVikingsJson Object 
   */
  public function fetch_access_token_via_xauth($userName, $userPassword) {
      $params = array();
      $params['x_auth_mode'] = 'client_auth';
      $params['x_auth_username'] = $userName;
      $params['x_auth_password'] = $userPassword;
      
      $response = $this->post($this->accessTokenUrl, $params);
  
      /* Extract parameters from url */
      $_params = explode('&', $response->responseText);
      $params = array();
      foreach($_params as $_param) {
        $param = explode('=', $_param);
        $params[$param[0]] = $param[1]; 
      }

      $this->setToken($params['oauth_token'], $params['oauth_token_secret']);

      return $response;
  }
  
  /**
   * get_msisdn_list
   * 
   * List all MSISDN's the authenticated user has access to.
   * 
   * @return string (json)
   */
  public function get_msisdn_list(){
      $response = $this->get("/msisdn_list.json", array('msisdn'=>''));
      return $response->responseText;
  }
  
  /**
   * get_price_plan_details
   *
   * Get the price plan details of the given mobile number.
   * The type and type_id represent a traffic type:
   *    1 is voice
   *    2 is data
   *    5 is SMS
   *    7 is MMS
   *    15 is SMS super-on-net (between Mobile Vikings)
   * 
   * @param type $msisdn
   * @return string (json) 
   */
  public function get_price_plan_details($msisdn = ''){
      $response = $this->get("/price_plan_details.json", array('msisdn'=>$msisdn));
      return $response->responseText;
  }
  
  /**
   * get_sim_balance
   * 
   * Get SIM balance of the given mobile number.
   * 
   * @param string $msisdn
   * @param integer $add_price_plan
   * 
   * @return string (json) 
   */
  public function get_sim_balance($msisdn = '', $add_price_plan = 0){
      $response = $this->get("/sim_balance.json", array('msisdn'=>$msisdn, 'add_price_plan'=>$add_price_plan));
      return $response->responseText;
  }
  
  /**
   * get_history
   * 
   * Get the top-up history of the authenticated user.
   * 
   * @param string $msisdn
   * @param string $from_date
   * @param string $until_date
   * @param integer $page_size
   * @param integer $page
   * 
   * @return string (json)
   */
  public function get_history($msisdn = '', $from_date = '', $until_date = '', $page_size = 25, $page = 1) {
      $params = array('msisdn'=>$msisdn, 'page_size'=> $page_size, 'page' => $page);

      if (!empty($from_date)) {
          $params['from_date'] = $from_date;
      }
      if (!empty($until_date)) {
          $params['until_date'] = $until_date;          
      }

      $response = $this->get("/top_up_history.json", $params);
      return $response->responseText;
  }

  /**
   * get_usage
   * 
   * Get the usage of the given mobile number. This is a list of calls, SMSes and data sessions.
   *
   * The duration_connection is the amount for which the customer was billed. 
   * For a voice call this is the length of the call, for a data connection 
   * it's the amount of bytes that went through the wire. 
   * The duration_call value is the total length of the session. 
   * E.g. for a voice connection it's duration_connection plus how long it took 
   * before the other side picked up the phone.
   * The duration_human contains a formatted string depending on the type (data, voice). 
   * If no senseable format can be determined, it shows n/a. For voice, it will be formatted as [HH:]MM:SS (with HH being optional). 
   * For data, it will be formatted to x MB (or GB or KB or bytes).
   * 
   * @param string $msisdn
   * @param string $from_date
   * @param string $until_date
   * @param integer $page_size
   * @param integer $page
   * @param integer $add_price_plan
   * 
   * @return string (json)
   */
  public function get_usage($msisdn = '', $from_date = '', $until_date = '', $page_size = 25, $page = 1, $add_price_plan = 0) {
      $params = array('msisdn'=>$msisdn, 'page_size'=> $page_size, 'page' => $page, 'add_price_plan'=>$add_price_plan);

      if (!empty($from_date)) {
          $params['from_date'] = $from_date;
      }
      if (!empty($until_date)) {
          $params['until_date'] = $until_date;          
      }

      $response = $this->get("/usage.json", $params);
      return $response->responseText;
  }
  
  /**
   * get_sim_info
   * 
   * Get the SIM card information for the given mobile number.
   *
   * @param string $msisdn
   * 
   * @return string (json) 
   */
  public function get_sim_info($msisdn = '') {
      $response = $this->get("/sim_info.json", array('msisdn'=>$msisdn));
      return $response->responseText;      
  }
  
  /**
   * get_stats
   * 
   * Get statistics about the Viking Points for the user.
   * 
   * @return string (json) 
   */
  public function get_stats() {
      $response = $this->get("/points/stats.json");
      return $response->responseText;      
  }
  
  /**
   * get_links
   * 
   * Get the referral link(s) for the user. It is possible a user has multiple links.
   * 
   * @return string (json) 
   */
  public function get_links() {
      $response = $this->get("/points/links.json");
      return $response->responseText;      
  }
  
  /**
   * get_referrals
   * 
   * Get a list of all the referrals the user created.
   * 
   * @param integer $page_size
   * @param integer $page
   * 
   * @return string (json)
   */
  public function get_referrals($page_size = 25, $page = 1) {
      $response = $this->get("/points/referrals.json", 
                                array('page_size'=>$page_size, 'page'=>$page));
      return $response->responseText;      
  }
  
  /***************************************************************************************************/
  
  /* OAuth methods */
  public function delete($endpoint, $params = null)
  {
    return $this->request('DELETE', $endpoint, $params);
  }

  public function get($endpoint, $params = null)
  {
    return $this->request('GET', $endpoint, $params);
  }

  public function post($endpoint, $params = null)
  {
    return $this->request('POST', $endpoint, $params);
  }

  /* Basic auth methods */
  public function delete_basic($endpoint, $params = null, $username = null, $password = null)
  {
    return $this->request_basic('DELETE', $endpoint, $params, $username, $password);
  }

  public function get_basic($endpoint, $params = null, $username = null, $password = null)
  {
    return $this->request_basic('GET', $endpoint, $params, $username, $password);
  }

  public function post_basic($endpoint, $params = null, $username = null, $password = null)
  {
    return $this->request_basic('POST', $endpoint, $params, $username, $password);
  }

  public function useApiVersion($version = null)
  {
    $this->apiVersion = $version;
  }

  public function useAsynchronous($async = true)
  {
    $this->isAsynchronous = (bool)$async;
  }

  public function __construct($consumerKey = null, $consumerSecret = null, $oauthToken = null, $oauthTokenSecret = null)
  {
    parent::__construct($consumerKey, $consumerSecret, self::EVOMOBILEVIKINGS_SIGNATURE_METHOD);
    $this->setToken($oauthToken, $oauthTokenSecret);
  }

  public function __call($name, $params = null/*, $username, $password*/)
  {
    $parts  = explode('_', $name);
    $method = strtoupper(array_shift($parts));
    $parts  = implode('_', $parts);
    $endpoint   = '/' . preg_replace('/[A-Z]|[0-9]+/e', "'/'.strtolower('\\0')", $parts) . '.json';
    /* HACK: this is required for list support that starts with a user id */
    $endpoint = str_replace('//','/',$endpoint);
    $args = !empty($params) ? array_shift($params) : null;

    // calls which do not have a consumerKey are assumed to not require authentication
    if($this->consumerKey === null)
    {
      $username = null;
      $password = null;

      if(!empty($params))
      {
        $username = array_shift($params);
        $password = !empty($params) ? array_shift($params) : null;
      }

      return $this->request_basic($method, $endpoint, $args, $username, $password);
    }

    return $this->request($method, $endpoint, $args);
  }

  private function getApiUrl($endpoint)
  {
    if(!empty($this->apiVersion))
      return "{$this->apiVersionedUrl}/api/{$this->apiVersion}/oauth{$endpoint}";
    else
      return "{$this->apiUrl}{$endpoint}";
  }

  private function request($method, $endpoint, $params = null)
  {
    $url = $this->getUrl($this->getApiUrl($endpoint));
    
    $resp= new EvoMobileVikingsJson(call_user_func(array($this, 'httpRequest'), $method, $url, $params, $this->isMultipart($params)), $this->debug);
    if(!$this->isAsynchronous)
      $resp->response;
   
    return $resp;
  }

  private function request_basic($method, $endpoint, $params = null, $username = null, $password = null)
  {
    $url = $this->getApiUrl($endpoint);
    
    if($method === 'GET')
      $url .= is_null($params) ? '' : '?'.http_build_query($params, '', '&');
    $ch  = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->requestTimeout);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if($method === 'POST' && $params !== null)
    {
      if($this->isMultipart($params))
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
      else
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->buildHttpQueryRaw($params));
    }
    if(!empty($username) && !empty($password))
      curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");

    $resp = new EvoMobileVikingsJson(EvoCurl::getInstance()->addCurl($ch), $this->debug);
    if(!$this->isAsynchronous)
      $resp->response;

    return $resp;
  }
}

class EvoMobileVikingsJson implements ArrayAccess, Countable, IteratorAggregate
{
  private $debug;
  private $__resp;
  public function __construct($response, $debug = false)
  {
    $this->__resp = $response;
    $this->debug  = $debug;
  }

  // ensure that calls complete by blocking for results, NOOP if already returned
  public function __destruct()
  {
    $this->responseText;
  }

  // Implementation of the IteratorAggregate::getIterator() to support foreach ($this as $...)
  public function getIterator ()
  {
    if ($this->__obj) {
      return new ArrayIterator($this->__obj);
    } else {
      return new ArrayIterator($this->response);
    }
  }

  // Implementation of Countable::count() to support count($this)
  public function count ()
  {
    return count($this->response);
  }
  
  // Next four functions are to support ArrayAccess interface
  // 1
  public function offsetSet($offset, $value) 
  {
    $this->response[$offset] = $value;
  }

  // 2
  public function offsetExists($offset) 
  {
    return isset($this->response[$offset]);
  }
  
  // 3
  public function offsetUnset($offset) 
  {
    unset($this->response[$offset]);
  }

  // 4
  public function offsetGet($offset) 
  {
    return isset($this->response[$offset]) ? $this->response[$offset] : null;
  }

  public function __get($name)
  {
    $accessible = array('responseText'=>1,'headers'=>1,'code'=>1);
    $this->responseText = $this->__resp->data;
    $this->headers      = $this->__resp->headers;
    $this->code         = $this->__resp->code;
    if(isset($accessible[$name]) && $accessible[$name])
      return $this->$name;
    elseif(($this->code < 200 || $this->code >= 400) && !isset($accessible[$name]))
      EvoMobileVikingsException::raise($this->__resp, $this->debug);

    // Call appears ok so we can fill in the response
    $this->response     = json_decode($this->responseText, 1);
    $this->__obj        = json_decode($this->responseText);

    if(gettype($this->__obj) === 'object')
    {
      foreach($this->__obj as $k => $v)
      {
        $this->$k = $v;
      }
    }

    if (property_exists($this, $name)) {
      return $this->$name;
    }
    return null;
  }

  public function __isset($name)
  {
    $value = self::__get($name);
    return !empty($name);
  }
}

class EvoMobileVikingsException extends Exception 
{
  public static function raise($response, $debug)
  {
    $message = $response->data;
    switch($response->code)
    {
      case 400:
        throw new EvoMobileVikingsBadRequestException($message, $response->code);
      case 401:
        throw new EvoMobileVikingsNotAuthorizedException($message, $response->code);
      case 403:
        throw new EvoMobileVikingsForbiddenException($message, $response->code);
      case 404:
        throw new EvoMobileVikingsNotFoundException($message, $response->code);
      case 406:
        throw new EvoMobileVikingsNotAcceptableException($message, $response->code);
      case 420:
        throw new EvoMobileVikingsEnhanceYourCalmException($message, $response->code);
      case 500:
        throw new EvoMobileVikingsInternalServerException($message, $response->code);
      case 502:
        throw new EvoMobileVikingsBadGatewayException($message, $response->code);
      case 503:
        throw new EvoMobileVikingsServiceUnavailableException($message, $response->code);
      default:
        throw new EvoMobileVikingsException($message, $response->code);
    }
  }
}
class EvoMobileVikingsBadRequestException extends EvoMobileVikingsException{}
class EvoMobileVikingsNotAuthorizedException extends EvoMobileVikingsException{}
class EvoMobileVikingsForbiddenException extends EvoMobileVikingsException{}
class EvoMobileVikingsNotFoundException extends EvoMobileVikingsException{}
class EvoMobileVikingsNotAcceptableException extends EvoMobileVikingsException{}
class EvoMobileVikingsEnhanceYourCalmException extends EvoMobileVikingsException{}
class EvoMobileVikingsInternalServerException extends EvoMobileVikingsException{}
class EvoMobileVikingsBadGatewayException extends EvoMobileVikingsException{}
class EvoMobileVikingsServiceUnavailableException extends EvoMobileVikingsException{}
