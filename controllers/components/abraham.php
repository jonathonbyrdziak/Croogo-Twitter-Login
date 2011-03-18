<?php
/**
 * 5Twenty Studios
 * Twitter Login
 *
 * This plugin is designed to make it possible for your users to login through twitter.
 * We aplogize, but this is an unsupported plugin. Feel free to contribute.
 *
 * @package Twitter Login
 * @subpackage Croogo
 * @author   Abraham Williams <abraham@abrah.am> || Jonathon Byrd <support@5twentystudios.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://abrah.am || http://www.5twentystudios.com
 * @repository https://github.com/Jonathonbyrd/Croogo-Twitter-Login
 */

//Load OAuth lib. You can find it at http://oauth.net
App::import('Vendor', 'Twitterlogin.OAuth', array('file' => 'OAuth'.DS.'OAuth.php'));
App::import('Core', 'http_socket');
uses('xml');


/**
 * Twitter OAuth class
 * 
 * The first PHP Library to support OAuth for Twitter's REST API. Modified by
 * 5Twenty Studios to allow for easy calling within cakephp.
 * 
 */
class AbrahamComponent extends Object
{
	/**
	 * Contains the last HTTP status code returned.
	 *
	 * @var integer
	 */
	public $http_code;
	
	/**
	 * Contains the last API call. 
	 *
	 * @var string
	 */
	public $url;
	
	/**
	 * Set up the API root URL.
	 *
	 * @var string
	 */
	public $host = "https://api.twitter.com/1/";
	
	/**
	 * Set timeout default.
	 *
	 * @var integer
	 */
	public $timeout = 30;
	
	/**
	 * Set connect timeout.
	 *
	 * @var integer
	 */
	public $connecttimeout = 30; 
	
	/**
	 * Verify SSL Cert.
	 *
	 * @var boolean
	 */
	public $ssl_verifypeer = FALSE;
	
	/**
	 * Respons format.
	 *
	 * @var string
	 */
	public $format = 'json';
	
	/**
	 * Decode returned json data.
	 *
	 * @var boolean
	 */
	public $decode_json = TRUE;
	
	/**
	 * Contains the last HTTP headers returned. 
	 *
	 * @var unknown_type
	 */
	public $http_info;
	
	/**
	 * Set the useragnet.
	 *
	 * @var string
	 */
	public $useragent = 'TwitterOAuth v0.2.0-beta2';
	
	/**
	 * Immediately retry the API call if the response was not successful.
	 *
	 * @var boolean
	 */
	public $retry = false;
	
	/**
	 * Are we connected to twitter?
	 *
	 * @var boolean
	 */
	public $authorized = false;
	
	/**
	 * Constructor.
	 * 
	 * AbrahamComponent / TwitterOAuth object
	 */
	function __construct()
	{
		// loading resources
    	global $twConfigs, $Abraham;
    	App::import('Model', 'Twitterlogin.Twitterlogin');
    	App::import('Model', 'Twitterlogin.Twprofile');
    	App::import('Component', 'Session');
    	
    	// reasons to return
    	if ( isset($twConfigs) && isset($this->consumer_key) ) return $twConfigs;
    	
    	// loading resources
    	$Twitterlogin = new Twitterlogin;
    	$Twprofile = new Twprofile;
    	$Session = new SessionComponent;
    	
    	// initializing variables
		$twConfigs = $Twitterlogin->findById(1);
    	$twConfigs = $twConfigs['Twitterlogin'];
    	
    	// populating class properties
    	$this->consumer_key = $twConfigs['consumer_key'];
    	$this->consumer_secret = $twConfigs['consumer_secret'];
    	$this->request_token_url = $twConfigs['request_token_url'];
    	$this->access_token_url = $twConfigs['access_token_url'];
    	$this->authorize_url = $twConfigs['authorize_url'];
    	$this->admin_twitter_username = $twConfigs['twitter_username'];
    	$this->admin_twitter_password = $twConfigs['twitter_password'];
    	
    	$auth = $Session->read('Auth');
		if ( isset($auth['User']['id']) ) // user is logged in, so automatically load accounts
		{
			$profile = $Twprofile->find('first',
					array('conditions'=> 
						array('Twprofile.croogo_id' => $auth['User']['id'])
						));
			
			if ( $profile )
			{
				$this->twitter_username = $profile['Twprofile']['username'];
				$this->oauth_token = $profile['Twprofile']['oauth_token'];
				$this->oauth_token_secret = $profile['Twprofile']['oauth_token_secret'];
				
				// make the connection
    			if ($this->initialize())
    			{
    				$this->authorized = true;
    			}
			}
		}
		
		// setting this as a global variable
		$Abraham = $this;
		
    	return $twConfigs;
	}
	
	/**
	 * get the auth token from this class
	 *
	 * @return unknown
	 */
	function getAuthToken( $token = false )
	{
		if (!isset($this->oauth_token)) return $token;
		if (is_null($this->oauth_token)) return $token;
		return $this->oauth_token;
	}
	
	/**
	 * get the auth token secret from this class
	 *
	 * @return unknown
	 */
	function getAuthTokenSecret( $token = false )
	{
		if (!isset($this->oauth_token_secret)) return $token;
		if (is_null($this->oauth_token_secret)) return $token;
		return $this->oauth_token_secret;
	}
	
	/**
	 * Function initializes this object with the access token
	 *
	 * @param unknown_type $oauth_token
	 * @param unknown_type $oauth_token_secret
	 */
	function initialize($oauth_token = NULL, $oauth_token_secret = NULL)
	{
		// initializing variables
		if (is_null($oauth_token) && $this->getAuthToken())
		{
			$oauth_token = $this->getAuthToken();
		}
		if (is_null($oauth_token_secret) && $this->getAuthTokenSecret())
		{
			$oauth_token_secret = $this->getAuthTokenSecret();
		}
		
		// reasons to return 
		if ( isset($this->token) && !is_null($this->token) ) return true;
		if ( is_null($oauth_token) ) return false;
		if ( is_null($oauth_token_secret) ) return false;
		
		// initializing resources
		$this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
		$this->consumer = new OAuthConsumer($this->consumer_key, $this->consumer_secret);
	
		// getting our session token
		if (!empty($oauth_token) && !empty($oauth_token_secret))
		{
			$this->token = new OAuthConsumer($oauth_token, $oauth_token_secret);
			return true;
		}
		else
		{
			$this->token = NULL;
			return false;
		}
		return false;
	}
	
	/**
	 * Function returns the users basic profile data
	 *
	 * @return unknown
	 */
	public function getProfile()
	{
		if ( ! $this->authorized ) return false;
		if ( ! $data = $this->get('account/verify_credentials') ) return false;
		return $data;
	}
	
	
	/**
	 * Set API URLS
	 */
	function accessTokenURL()
	{ 
		return (isset($this->access_token_url)) ?$this->access_token_url :'https://api.twitter.com/oauth/access_token'; 
	}
	function authenticateURL()
	{ 
		return 'https://twitter.com/oauth/authenticate'; 
	}
	function authorizeURL()
	{ 
		return (isset($this->authorize_url)) ?$this->authorize_url :'https://twitter.com/oauth/authorize'; 
	}
	function requestTokenURL()
	{ 
		return (isset($this->request_token_url)) ?$this->request_token_url :'https://api.twitter.com/oauth/request_token'; 
	}
	
	/**
	 * Get a request_token from Twitter
	 *
	 * @param unknown_type $oauth_callback
	 * @return a key/value array containing oauth_token and oauth_token_secret
	 */
	function getRequestToken($oauth_callback = NULL) {
		$parameters = array();
		if (!empty($oauth_callback)) {
			$parameters['oauth_callback'] = $oauth_callback;
		} 
		$request = $this->oAuthRequest($this->requestTokenURL(), 'GET', $parameters);
		$token = OAuthUtil::parse_parameters($request);
		$this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
		return $token;
	}

	/**
	 * Get the authorize URL
	 *
	 * @param unknown_type $token
	 * @param unknown_type $sign_in_with_twitter
	 * @return unknown
	 */
	function getAuthorizeURL($token, $sign_in_with_twitter = TRUE) {
		if (is_array($token)) {
			$token = $token['oauth_token'];
		}
		if (empty($sign_in_with_twitter)) {
			return $this->authorizeURL() . "?oauth_token={$token}";
		} else {
			 return $this->authenticateURL() . "?oauth_token={$token}";
		}
	}

	/**
	 * Exchange request token and secret for an access token and
	 * secret, to sign API calls.
	 *
	 * @param unknown_type $oauth_verifier
	 * @returns array("oauth_token" => "the-access-token",
	 *								"oauth_token_secret" => "the-access-secret",
	 *								"user_id" => "9436992",
	 *								"screen_name" => "abraham")
	 *
	 */
	function getAccessToken($oauth_verifier = FALSE) {
		$parameters = array();
		if (!empty($oauth_verifier)) {
			$parameters['oauth_verifier'] = $oauth_verifier;
		}
		$request = $this->oAuthRequest($this->accessTokenURL(), 'GET', $parameters);
		$token = OAuthUtil::parse_parameters($request);
		$this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
		return $token;
	}

	/**
	 * One time exchange of username and password for access token and secret.
	 * 
	 * @param unknown_type $username
	 * @param unknown_type $password
	 * @return array("oauth_token" => "the-access-token",
	 *								"oauth_token_secret" => "the-access-secret",
	 *								"user_id" => "9436992",
	 *								"screen_name" => "abraham",
	 *								"x_auth_expires" => "0")
	 */	
	function getXAuthToken($username, $password) {
		$parameters = array();
		$parameters['x_auth_username'] = $username;
		$parameters['x_auth_password'] = $password;
		$parameters['x_auth_mode'] = 'client_auth';
		$request = $this->oAuthRequest($this->accessTokenURL(), 'POST', $parameters);
		$token = OAuthUtil::parse_parameters($request);
		$this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
		return $token;
	}

	/**
	 * GET wrapper for oAuthRequest.
	 *
	 * @param unknown_type $url
	 * @param unknown_type $parameters
	 * @return unknown
	 */
	function get($url, $parameters = array()) {
		$response = $this->oAuthRequest($url, 'GET', $parameters);
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response);
		}
		return $response;
	}
	
	/**
	 * POST wrapper for oAuthRequest.
	 *
	 * @param unknown_type $url
	 * @param unknown_type $parameters
	 * @return unknown
	 */
	function post($url, $parameters = array()) {
		$response = $this->oAuthRequest($url, 'POST', $parameters);
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response);
		}
		return $response;
	}

	/**
	 * DELETE wrapper for oAuthReqeust.
	 *
	 * @param unknown_type $url
	 * @param unknown_type $parameters
	 * @return unknown
	 */
	function delete($url, $parameters = array()) {
		$response = $this->oAuthRequest($url, 'DELETE', $parameters);
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response);
		}
		return $response;
	}

	/**
	 * Format and sign an OAuth / API request
	 *
	 * @param unknown_type $url
	 * @param unknown_type $method
	 * @param unknown_type $parameters
	 * @return unknown
	 */
	function oAuthRequest($url, $method, $parameters)
	{
		if (strrpos($url, 'https://') !== 0 && strrpos($url, 'http://') !== 0)
		{
			$url = "{$this->host}{$url}.{$this->format}";
		}
		
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters);
		$request->sign_request($this->sha1_method, $this->consumer, $this->token);
		switch ($method)
		{
			case 'GET':
				return $this->http($request->to_url(), 'GET');
			default:
				return $this->http($request->get_normalized_http_url(), $method, $request->to_postdata());
		}
	}

	/**
	 * Make an HTTP request
	 *
	 * @param unknown_type $url
	 * @param unknown_type $method
	 * @param unknown_type $postfields
	 * @return unknown
	 */
	function http($url, $method, $postfields = NULL) {
		$this->http_info = array();
		$ci = curl_init();
		/* Curl settings */
		curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
		curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);

		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (!empty($postfields)) {
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
				}
				break;
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if (!empty($postfields)) {
					$url = "{$url}?{$postfields}";
				}
		}
	
		curl_setopt($ci, CURLOPT_URL, $url);
		$response = curl_exec($ci);
		$this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		$this->http_info = array_merge($this->http_info, curl_getinfo($ci));
		$this->url = $url;
		curl_close ($ci);
			
		return $response;
	}

	/**
	 * Get the header info to store.
	 *
	 * @param unknown_type $ch
	 * @param unknown_type $header
	 * @return unknown
	 */
	function getHeader($ch, $header) {
		$i = strpos($header, ':');
		if (!empty($i)) {
			$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
			$value = trim(substr($header, $i + 2));
			$this->http_header[$key] = $value;
		}
		return strlen($header);
	}
	
	/**
	 * Debug helpers
	 */
	function lastStatusCode() { return $this->http_status; }
	function lastAPICall() { return $this->last_api_call; }
	
}




/*
 * Copyright (c) <2008> Justin Poliey <jdp34@njit.edu>
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

/**
 * Twitterlibphp is a PHP implementation of the Twitter API, allowing you
 * to take advantage of it from within your PHP applications.
 *
 * @author Justin Poliey <jdp34@njit.edu>
 * @package twitterlibphp
 * @repository https://github.com/jdp/twitterlibphp/blob/master/twitter.lib.php
 */

/**
 * Twitter API abstract class
 * @package twitterlibphp
 */
abstract class TwitterBase {

	/**
	 * the last HTTP status code returned
	 * @access private
	 * @var integer
	 */
	private $http_status;

	/**
	 * the whole URL of the last API call
	 * @access private
	 * @var string
	 */
	private $last_api_call;

	/**
	 * the application calling the API
	 * @access private
	 * @var string
	 */
	private $application_source;

	/**
	 * Returns the 20 most recent statuses from non-protected users who have set a custom user icon.
	 * @param string $format Return format
	 * @return string
	 */
	function getPublicTimeline($format = 'xml') {
		return $this->apiCall('statuses/public_timeline', 'get', $format, array(), false);
	}

	/**
	 * Returns the 20 most recent statuses posted by the authenticating user and that user's friends.
	 * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function getFriendsTimeline($options = array(), $format = 'xml') {
		return $this->apiCall('statuses/friends_timeline', 'get', $format, $options);
	}

	/**
	 * Returns the 20 most recent statuses posted from the authenticating user.
	 * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function getUserTimeline($options = array(), $format = 'xml') {
		return $this->apiCall('statuses/user_timeline', 'get', $format, $options, true);
	}

  /**
   * Returns the 20 most recent mentions (status containing @username) for the authenticating user.
   * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
  function getMentions($options = array(), $format = 'xml') {
    return $this->apiCall("statuses/mentions", 'get', $format, $options);
  }

  /**
	 * Returns the 20 most recent @replies (status updates prefixed with @username) for the authenticating user.
	 * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
   * @deprecated
	 */
	function getReplies($options = array(), $format = 'xml') {
		return $this->apiCall('statuses/replies', 'get', $format, $options);
	}

	/**
	 * Returns a single status, specified by the $id parameter.
	 * @param string|integer $id The numerical ID of the status to retrieve
	 * @param string $format Return format
	 * @return string
	 */
	function getStatus($id, $format = 'xml') {
		return $this->apiCall("statuses/show/{$id}", 'get', $format, array(), false);
	}

	/**
	 * Updates the authenticated user's status.
	 * @param string $status Text of the status, no URL encoding necessary
	 * @param string|integer $reply_to ID of the status to reply to. Optional
	 * @param string $format Return format
	 * @return string
	 */
	function updateStatus($status, $reply_to = null, $format = 'xml') {
		$options = array('status' => $status);
		if ($reply_to) {
			$options['in_reply_to_status_id'] = $reply_to;
		}
    	return $this->apiCall('statuses/update', 'post', $format, $options);
	}

	/**
	 * Destroys the status specified by the required ID parameter. The authenticating user must be the author of the specified status.
	 * @param integer|string $id ID of the status to destroy
	 * @param string $format Return format
	 * @return string
	 */
	function destroyStatus($id, $format = 'xml') {
    	return $this->apiCall("statuses/destroy/{$id}", 'post', $format, $options);
	}

	/**
	 * Returns the authenticating user's friends, each with current status inline.
	 * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function getFriends($options = array(), $format = 'xml') {
		return $this->apiCall('statuses/friends', 'get', $format, $options, false);
	}

	/**
	 * Returns the authenticating user's followers, each with current status inline.
	 * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function getFollowers($options = array(), $format = 'xml') {
		return $this->apiCall('statuses/followers', 'get', $format, $options);
	}

	/**
	 * Returns extended information of a given user.
	 * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function showUser($options = array(), $format = 'xml') {
		if (!array_key_exists('id', $options) && !array_key_exists('user_id', $options) && !array_key_exists('screen_name', $options)) {
			$options['id'] = substr($this->credentials, 0, strpos($this->credentials, ':'));
		}
		return $this->apiCall('users/show', 'get', $format, $options, false);
	}

	/**
	 * Returns a list of the 20 most recent direct messages sent to the authenticating user.
	 * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function getMessages($options = array(), $format = 'xml') {
		return $this->apiCall('direct_messages', 'get', $format, $options);
	}

	/**
	 * Returns a list of the 20 most recent direct messages sent by the authenticating user.
	 * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function getSentMessages($options = array(), $format = 'xml') {
		return $this->apiCall('direct_messages/sent', 'get', $format, $options);
	}

	/**
	 * Sends a new direct message to the specified user from the authenticating user.
	 * @param string $user The ID or screen name of a recipient
	 * @param string $text The message to send
	 * @param string $format Return format
	 * @return string
	 */
	function newMessage($user, $text, $format = 'xml') {
		$options = array(
			'user' => $user,
			'text' => $text
		);
		return $this->apiCall('direct_messages/new', 'post', $format, $options);
	}

	/**
	 * Destroys the direct message specified in the required $id parameter.
	 * @param integer|string $id The ID of the direct message to destroy
	 * @param string $format Return format
	 * @return string
	 */
	function destroyMessage($id, $format = 'xml') {
		return $this->apiCall("direct_messages/destroy/{$id}", 'post', $format, $options);
	}

	/**
	 * Befriends the user specified in the ID parameter as the authenticating user.
	 * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function createFriendship($options = array(), $format = 'xml') {
		if (!array_key_exists('follow', $options)) {
      $options['follow'] = 'true';
    }
		return $this->apiCall('friendships/create', 'post', $format, $options);
	}

	/**
	 * Discontinues friendship with the user specified in the ID parameter as the authenticating user.
	 * @param integer|string $id The ID or screen name of the user to unfriend
	 * @param string $format Return format
	 * @return string
	 */
	function destroyFriendship($id, $format = 'xml') {
		$options = array('id' => $id);
		return $this->apiCall('friendships/destroy', 'post', $format, $options);
	}

	/**
	 * Tests if a friendship exists between two users.
	 * @param integer|string $user_a The ID or screen name of the first user
	 * @param integer|string $user_b The ID or screen name of the second user
	 * @param string $format Return format
	 * @return string
	 */
	function friendshipExists($user_a, $user_b, $format = 'xml') {
		$options = array(
			'user_a' => $user_a,
			'user_b' => $user_b
		);
		return $this->apiCall('friendships/exists', 'get', $format, $options);
	}

	/**
	 * Returns an array of numeric IDs for every user the specified user is followed by.
	 * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function getFriendIDs($options = array(), $format = 'xml') {
		return $this->apiCall('friends/ids', 'get', $format, $options);
	}

	/**
	 * Returns an array of numeric IDs for every user the specified user is following.
	 * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function getFollowerIDs($options = array(), $format = 'xml') {
		return $this->apiCall('followers/ids', 'get', $format, $options);
	}

	/**
	 * Returns an HTTP 200 OK response code and a representation of the requesting user if authentication was successful; returns a 401 status code and an error message if not.
	 * @param string $format Return format
	 * @return string
	 */
	function verifyCredentials($format = 'xml') {
		return $this->apiCall('account/verify_credentials', 'get', $format, array());
	}

  /**
	 * Returns the remaining number of API requests available to the requesting user before the API limit is reached for the current hour.
	 * @param boolean $authenticate Authenticate before calling method
   * @param string $format Return format
	 * @return string
	 */
	function rateLimitStatus($authenticate = false, $format = 'xml') {
		return $this->apiCall('account/rate_limit_status', 'get', $format, array(), $authenticate);
	}

	/**
	 * Ends the session of the authenticating user, returning a null cookie.
	 * @param string $format Return format
	 * @return string
	 */
	function endSession($format = 'xml') {
		return $this->apiCall('account/end_session', 'post', $format, array());
	}

	/**
	 * Sets which device Twitter delivers updates to for the authenticating user.
	 * @param string $device The delivery device used. Must be sms, im, or none
	 * @return string
	 */
	function updateDeliveryDevice($device, $format = 'xml') {
		$options = array('device' => $device);
		return $this->apiCall('account/update_delivery_device', 'post', $format, $options);
	}

	/**
	 * Sets one or more hex values that control the color scheme of the authenticating user's profile page on twitter.com.
	 * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function updateProfileColors($options, $format = 'xml') {
		return $this->apiCall('account/update_profile_colors', 'post', $format, $options);
	}

	/**
	 * Sets values that users are able to set under the "Account" tab of their settings page.
	 * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function updateProfile($options, $format = 'xml') {
		return $this->apiCall('account/update_profile', 'post', $format, array());
	}


	/**
	 * Returns the 20 most recent favorite statuses for the authenticating user or user specified by the ID parameter in the requested format.
	 * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function getFavorites($options = array(), $format = 'xml') {
		return $this->apiCall('favorites', 'get', $format, $options);
	}

	/**
	 * Favorites the status specified in the ID parameter as the authenticating user.
	 * @param integer|string $id The ID of the status to favorite
	 * @param string $format Return format
	 * @return string
	 */
	function createFavorite($id, $format = 'xml') {
		return $this->apiCall("favorites/create/{$id}", 'post', $format, array());
	}

	/**
	 * Un-favorites the status specified in the ID parameter as the authenticating user.
	 * @param integer|string $id The ID of the status to un-favorite
	 * @param string $format Return format
	 * @return string
	 */
	function destroyFavorite($id, $format = 'xml') {
		return $this->apiCall("favorites/destroy/{$id}", 'post', $format, array());
	}

	/**
	 * Enables notifications for updates from the specified user to the authenticating user.
	 * @param integer|string $id The ID or screen name of the user to follow
	 * @param string $format Return format
	 * @return string
	 */
	function follow($id, $format = 'xml') {
		$options = array('id' => $id);
		return $this->apiCall('notifications/follow', 'post', $format, $options);
	}

	/**
	 * Disables notifications for updates from the specified user to the authenticating user.
	 * @param integer|string $id The ID or screen name of the user to leave
	 * @param string $format Return format
	 * @return string
	 */
	function leave($id, $format = 'xml') {
		$options = array('id' => $id);
		return $this->apiCall('notifications/leave', 'post', $format, $options);
	}

	/**
	 * Blocks the user specified in the ID parameter as the authenticating user.
	 * @param integer|string $id The ID or screen name of the user to block
	 * @param string $format Return format
	 * @return string
	 */
	function createBlock($id, $format = 'xml') {
		$options = array('id' => $id);
		return $this->apiCall('blocks/create', 'post', $format, $options);
	}

	/**
	 * Unblocks the user specified in the ID parameter as the authenticating user.
	 * @param integer|string $id The ID or screen name of the user to unblock
	 * @param string $format Return format
	 * @return string
	 */
	function destroyBlock($id, $format = 'xml') {
		$options = array('id' => $id);
		return $this->apiCall('blocks/destroy', 'post', $format, $options);
	}

  /**
	 * Returns if the authenticating user is blocking a target user.
	 * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function blockExists($options, $format = 'xml') {
		return $this->apiCall('blocks/exists', 'get', $format, $options);
	}

  /**
	 * Returns an array of user objects that the authenticating user is blocking.
   * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function getBlocking($options, $format = 'xml') {
		return $this->apiCall('blocks/blocking', 'get', $format, $options);
	}

  /**
	 * Returns an array of numeric user ids the authenticating user is blocking.
   * @param array $options Options to pass to the method
	 * @param string $format Return format
	 * @return string
	 */
	function getBlockingIDs($format = 'xml') {
		return $this->apiCall('blocks/blocking/ids', 'get', $format, array());
	}

	/**
	 * Returns the string "ok" in the requested format with a 200 OK HTTP status code.
	 * @param string $format Return format
	 * @return string
	 */
	function test($format = 'xml') {
		return $this->apiCall('help/test', 'get', $format, array(), false);
	}

	/**
	 * Returns the last HTTP status code
	 * @return integer
	 */
	function lastStatusCode() {
		return $this->http_status;
	}

	/**
	 * Returns the URL of the last API call
	 * @return string
	 */
	function lastApiCall() {
		return $this->last_api_call;
	}
}

/**
 * Access to the Twitter API through HTTP auth
 * @package twitterlibphp
 */
class Twitter extends TwitterBase {

	/**
	 * the Twitter credentials in HTTP format, username:password
	 * @access private
	 * @var string
	 */
	var $credentials;

	/**
	 * Fills in the credentials {@link $credentials} and the application source {@link $application_source}.
	 * @param string $username Twitter username
	 * @param string $password Twitter password
	 * @param $source string Optional. Name of the application using the API
	 */
	function __construct($username, $password, $source = null) {
		$this->credentials = sprintf("%s:%s", $username, $password);
		$this->application_source = $source;
	}

	/**
	 * Executes an API call
	 * @param string $twitter_method The Twitter method to call
   * @param string $http_method The HTTP method to use
   * @param string $format Return format
   * @param array $options Options to pass to the Twitter method
	 * @param boolean $require_credentials Whether or not credentials are required
	 * @return string
	 */
	protected function apiCall($twitter_method, $http_method, $format, $options, $require_credentials = true) {
		$curl_handle = curl_init();
    $api_url = sprintf('http://twitter.com/%s.%s', $twitter_method, $format);
    if (($http_method == 'get') && (count($options) > 0)) {
      $api_url .= '?' . http_build_query($options);
    }
    //echo $api_url . "\n";
		curl_setopt($curl_handle, CURLOPT_URL, $api_url);
		if ($require_credentials) {
			curl_setopt($curl_handle, CURLOPT_USERPWD, $this->credentials);
		}
		if ($http_method == 'post') {
			curl_setopt($curl_handle, CURLOPT_POST, true);
      curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($options));
		}
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Expect:'));
		$twitter_data = curl_exec($curl_handle);
		$this->http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		$this->last_api_call = $api_url;
		curl_close($curl_handle);
		return $twitter_data;
	}

}

/**
 * TODO: Add TwitterOAuth class
 */
?>