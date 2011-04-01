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
 * @author   Jonathon Byrd <support@5twentystudios.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.5twentystudios.com
 * @repository https://github.com/Jonathonbyrd/Croogo-Twitter-Login
 * 
 */
/**
 * Twitter login Controller
 * 
 * ERRORS I EXPERIENCED
 * ====================
 * 
 * Failed to authenticate token.
 * Twitter says that this should never happen. However I was able to make it happen.
 * I caused this error by having the wrong callback URL. I sent a twitter url as 
 * parameter to the $this->Abraham->getRequestToken(  ); function.
 * 
 * Failed to validate oauth signature and token
 * Many people say that it was their server clock's synchronization. Turned out to be
 * the already authorized user tokens. So I deleted the application and created a new
 * twitter application, then deleted all of the user tokens and this fixed the problem.
 * 
 * Sessions not saving.
 * In the cake core configurations file I noticed that the cake set session id had a
 * different name then the session itself. It was almost like the session would start
 * as normal and then cake would reload it with their name, loosing all the values. I
 * fixed this by changing the cake session id back to default PHPSESSID
 * 
 * Sessions not saving.
 * Had a second issue with sessions, so I ended up running the sessions.sql file provided
 * by cakephp and then adjusting the configurations file for sessions to be saved to
 * the database. I like this better anyway.
 * 
 * 401 error on requesting auth_token.
 * I wasn't sending all of the variables to the auth_token url, therefore I was receiving
 * a 401 error.
 * 
 * 
 * GET THE CODE
 * ====================
 * You can pull the latest development version using git:
 * git clone git://github.com/abraham/twitteroauth.git
 * 
 * Or you can download the latest release by visiting:
 * http://github.com/abraham/twitteroauth/downloads
 * 
 * 
 * FLOW OVERVIEW
 * ====================
 * 1) Build TwitterOAuth object using client credentials.
 * 2) Request temporary credentials from Twitter.
 * 3) Build authorize URL for Twitter.
 * 4) Redirect user to authorize URL.
 * 5) User authorizes access and returns from Twitter.
 * 6) Rebuild TwitterOAuth object with client credentials and temporary credentials.
 * 7) Get token credentials from Twitter.
 * 8) Rebuild TwitterOAuth object with client credentials and token credentials.
 * 9) Query Twitter API.
 * 
 * 
 * TERMINOLOGY
 * ====================
 * The terminology has changed since 0.1.x to better match the draft-hammer-oauth IETF
 * RFC. You can read that at http://tools.ietf.org/html/draft-hammer-oauth. Some of the
 * terms will differ from those Twitter uses as well.
 * 
 * client credentials - Consumer key/secret you get when registering an app with Twitter.
 * temporary credentials - Previously known as the request token.
 * token credentials - Previously known as the access token.
 * 
 * 
 */
class TwitterloginController extends TwitterloginAppController
{
	/**
	 * Controller name
	 *
	 * @var string
	 * @access public
	 */
    public $name = 'Twitterlogin';
    
	/**
	 * additional components
	 *
	 * @var unknown_type
	 */
	public $components = array(
		'Security',
		'Twitterlogin.Abraham',
		'Twitterlogin.Twitterlogin',
		'Session',
		'Auth',
	);
    
	/**
	 * Models used by the Controller
	 *
	 * @var array
	 * @access public
	 */
	public $uses = array(
		'Twitterlogin',
		'User',
		'Twprofile',
	);
    
	/**
	 * Models used by the Controller
	 *
	 * @var array
	 * @access public
	 */
	public $helpers = array(
        'Html',
		'Twitterlogin.Twitterlogin',
	);
	
	/**
	 * Successful login redirect page
	 * 
	 * This is the default page that the user will be redirected to upon a 
	 * successful transaction. This is given to Router::url() as a parameter
	 *
	 * @var string
	 */
	public $loginRedirect = '/';
	
    /**
     * Before filter callback,
     * disable CSFR security check to avoid security error
     *
     * @return void
     */
    function beforeFilter()
    {
        parent::beforeFilter();
        $this->Security->validatePost = false;
        
        if ($this->Session->read("Twitter.redirect")) 
        {
        	$this->loginRedirect = $this->Session->read("Twitter.redirect");
        }
    }

	/**
	 * Send the user to twitter
	 *
	 * 0) Users start out on connect.php which displays the "Sign in with Twitter" image hyperlinked
	 * to redirect.php. This button should be displayed on your homepage in your login section. The
	 * client credentials are saved in config.php as CONSUMER_KEY and CONSUMER_SECRET. You can
	 * save a static callback URL in the app settings page, in the config file or use a dynamic
	 * callback URL later in step 2. In example use http://example.com/callback.php.
	 * 
	 * 1) When a user lands on redirect.php we build a new TwitterOAuth object using the client 
	 * credentials. If you have your own configuration method feel free to use it instead of config.php.
	 * $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);// Use config.php client credentials
	 * $connection = new TwitterOAuth('abc890', '123xyz');
	 * 
	 * 2) Using the built $connection object you will ask Twitter for temporary credentials. If you
	 * wish to have a dynamic callback URL for each user you can do pass a URL as a parameter.
	 * $temporary_credentials = $connection->getRequestToken(); // Use applications registered callback.
	 * $temporary_credentials = $connection->getRequestToken('http://example.com/callback.php?');
	 * 
	 * 3) Now that we have temporary credentials the user has to go to Twitter and authorize the app
	 * to access and updates their data. You can also pass a second parameter of FALSE to not use Sign
	 * in with Twitter: http://apiwiki.twitter.com/Sign-in-with-Twitter.
	 * $redirect_url = $connection->getAuthorizeURL($temporary_credentials); // Use Sign in with Twitter
	 * $redirect_url = $connection->getAuthorizeURL($temporary_credentials, FALSE);
	 * 
	 * 4) You will now have a Twitter URL that you must send the user to. You can add parameters and
	 * they will return with the user in step 5.
	 * https://twitter.com/oauth/authenticate?oauth_token=xyz123
	 * https://twitter.com/oauth/authenticate?oauth_token=xyz123&info=abc // info will return with user
	 * 
	 * 5) The user is now on twitter.com and may have to login. Once authenticated with Twitter they will
	 * will either have to click on allow/deny, or will be automatically redirected back to the callback.
	 * 
	 * @param unknown_type $type
	 */
	function authorize( $type = 'twitter' )
	{
		// Build TwitterOAuth object with client credentials.
		$this->Abraham->initialize();
		
		// Get temporary credentials.
		//send callback url as parameter, or leave false to use the registered APP callback
		$request_token = $this->Abraham->getRequestToken(  );
		
		// Save temporary credentials to session.
		$this->Session->write("Twitter.oauth_token", $request_token['oauth_token']);
		$this->Session->write("Twitter.oauth_token_secret", $request_token['oauth_token_secret']);
		
		// If last connection failed don't display authorization link.
		switch ($this->Abraham->http_code)
		{
		  case 200:
		    // Build authorize URL and redirect user to Twitter.
		    $url = $this->Abraham->getAuthorizeURL( $request_token['oauth_token'] );
		    header("Location: " . $url); 
		    break;
		    
		  default:
		    // Show notification if something went wrong.
		  	$this->Session->setFlash(__('Something went wrong, we couldnt connect to twitter.', true), 'default', array('class' => 'error'));
		  	$loginPage = Router::url(array('plugin' => null, 'controller' => 'users', 'action' => 'login'), true);
		    header("Location: " . $loginPage);
		    break;
		}
		exit();
	}
	
    /**
     * Twitter sends the user back to us
     * 
     * 6) Now that the user has returned to callback.php and allowed access we need to build a new 
     * TwitterOAuth object using the temporary credentials.
     * $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], 
     * $_SESSION['oauth_token_secret']);
     * 
     * 7) Now we ask Twitter for long lasting token credentials. These are specific to the application 
     * and user and will act like password to make future requests. If a dynamic callback URL was used 
     * you will also have to pass the oauth_varifier parameter. Normally the token credentials would 
     * get saved in your database but for this example we are just using sessions.
     * $token_credentials = $connection->getAccessToken(); // Used applications registered callback URL
     * $token_credentials = $connection->getAccessToken($_REQUEST['oauth_verifier']);
     * 
     * 7a) After getting the token credentials we redirect the user to index.php.
     * 
     * 8) With the token credentials we build a new TwitterOAuth object.
     * $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $token_credentials['oauth_token'], 
     * $token_credentials['oauth_token_secret']);
     * 
     * 9) And finally we can make requests authenticated as the user. You can GET, POST, and DELETE API
     * methods. Directly copy the path from the API documentation and add an array of any parameter
     * you wish to include for the API method such as curser or in_reply_to_status_id.
     * $content = $connection->get('account/verify_credentials');
     * $connection->post('statuses/update', array('status' => 'Text of status here',
     * 'in_reply_to_status_id' => 123456));
     * $content = $connection->delete('statuses/destroy/12345');
     * 
     * @link http://www.yourdomain.com/twitterlogin/callback
     */
    public function callback( $type = 'twitter' )
    {
    	// If the oauth_token is old redirect to the connect page.
		if ( $this->Session->read('Twitter.oauth_token') !== $this->params['url']['oauth_token'] )
		{
			$this->Session->write('Twitter.oauth_status', 'oldtoken');
			$this->Session->destroy();
			
			$this->Session->setFlash(__('Sorry, we had to clear your cache. Please try again.', true), 'default', array('class' => 'error'));
			$loginPage = Router::url(array('plugin' => null, 'controller' => 'users', 'action' => 'login'), true);
		    header("Location: " . $loginPage);
		    exit();
		}
		
		// Create TwitteroAuth object with app key/secret and token key/secret from default phase
		$this->Abraham->initialize($this->Session->read('Twitter.oauth_token'), $this->Session->read('Twitter.oauth_token_secret'));
		
		// Request access tokens from twitter
		$access_token = $this->Abraham->getAccessToken($this->params['url']['oauth_verifier']);
		
		// Something failed, we dont have any tokens
		if ( empty($access_token['oauth_token']) && empty($access_token['oauth_token_secret']) )
		{
			// Save HTTP status for error dialog on connnect page.
			$this->Session->write('Twitter.oauth_status', 'oldtoken');
			$this->Session->destroy();
			
			$this->Session->setFlash(__('Sorry, we didnt receive any Twitter tokens.', true), 'default', array('class' => 'error'));
			$loginPage = Router::url(array('plugin' => null, 'controller' => 'users', 'action' => 'login'), true);
		    header("Location: " . $loginPage);
		    exit();
		}
		
		// If HTTP response is 200 continue otherwise send to connect page to retry
		if ( 200 != $this->Abraham->http_code )
		{
			// Save HTTP status for error dialog on connnect page.
			$this->Session->write('Twitter.oauth_status', 'oldtoken');
			$this->Session->destroy();
			
			$this->Session->setFlash(__('Sorry, we had to clear your cache. Please try again.', true), 'default', array('class' => 'error'));
			$loginPage = Router::url(array('plugin' => null, 'controller' => 'users', 'action' => 'login'), true);
		    header("Location: " . $loginPage);
		    exit();
		}
		
		$Twprofile = $this->Twprofile->find('first',
					array('conditions'=> 
						array('Twprofile.oauth_token' => $access_token['oauth_token'],
						'Twprofile.oauth_token_secret' => $access_token['oauth_token_secret'])
						));
		
		// If the twitter connect information does not exist
		if (!$Twprofile) 
		{
			// Save the twitter connect details 
			$Twprofile = array();
			$Twprofile['Twprofile'] = array();
			$Twprofile['Twprofile']['twitter_id'] = $access_token['user_id'];
			$Twprofile['Twprofile']['oauth_token'] = $access_token['oauth_token'];
			$Twprofile['Twprofile']['oauth_token_secret'] = $access_token['oauth_token_secret'];
			$Twprofile['Twprofile']['username'] = $access_token['screen_name'];
			
			$this->Twprofile->create( );
			if ( ! $this->Twprofile->save( $Twprofile ) )
			{
				$this->Session->setFlash(__('Saving to the database failed. Please try again.', true), 'default', array('class' => 'error'));
				$loginPage = Router::url(array('plugin' => null, 'controller' => 'users', 'action' => 'login'), true);
			    header("Location: " . $loginPage);
			    exit();
			}
		}
		
		// Remove no longer needed request tokens 
		$this->Session->delete('Twitter.oauth_token');
		$this->Session->delete('Twitter.oauth_token_secret');
		
		$this->Session->write('Twitter.id', $Twprofile['Twprofile']['twitter_id']);
		$this->Session->write('Twitter.oauth_status', 'verified');
		
		// The account is not connected to croogo
		if ( empty($Twprofile['Twprofile']['croogo_id']) )
		{
			$auth = $this->Session->read('Auth');
			if ($auth['User']['id']) // user is logged in, so automatically merge accounts
			{
				$Twprofile['Twprofile']['croogo_id'] = $auth['User']['id'];
				$this->Twprofile->save( $Twprofile );
				
				// ALL DONE!
				$this->Session->setFlash(__('Logged In!', true), 'default', array('class' => 'success'));
				$homePage = Router::url($this->loginRedirect, true);
				header("Location: " . $homePage);
				exit();
			}
			
			// user needs to merge accounts
			$lastStep = Router::url(array('plugin' => null, 'controller' => 'twitterlogin', 'action' => 'laststep'), true);
			header("Location: " . $lastStep);
			exit();
		}
		
		// authenticate the user
		$User = $this->User->find('first',
					array('conditions'=> 
						array('User.id' => $Twprofile['Twprofile']['croogo_id'])
						));
		
		// We couldn't find the user
		if (!$User)
		{
			$lastStep = Router::url(array('plugin' => null, 'controller' => 'twitterlogin', 'action' => 'laststep'), true);
			header("Location: " . $lastStep);
			exit();
		}
		
		//and this is an authenticated, logged in, happy user
		$this->Auth->fields = array('username' => 'id', 'password' => 'email'); 
		$this->Auth->autoRedirect = false; 
		$this->Auth->user( $User['User']['id'] );
		$this->Auth->login( $User['User'] );
		
		
		$this->Session->setFlash(__('Logged In!', true), 'default', array('class' => 'success'));
		$homePage = Router::url($this->loginRedirect, true);
		header("Location: " . $homePage);
		exit();
    }
	
	/**
	 * This step asks the user for his email address.
	 * 
	 * We need to connect the users twitter account to their croogo account OR
	 * We need to create them a new account if one doesn't exist, using their
	 * twitter information.
	 * 
	 * @TODO Create the user or attach the user to this account
	 * 
	 * @return null
	 */
	public function laststep()
	{
		// initializing view variables
		if ( $this->Session->read('Twitter.id') )
		{
			$this->set('twitter_id', $this->Session->read('Twitter.id'));
		}
		elseif ( isset($this->data['Twitterlogin']['twitter_id']) )
		{
			$this->Session->write('Twitter.id', $this->data['Twitterlogin']['twitter_id']);
			$this->set('twitter_id', $this->data['Twitterlogin']['twitter_id']);
		}
		
		
		// if the user is connecting their email
		if ( isset($this->data['Twitterlogin']['email']) && !empty($this->data['Twitterlogin']['email']) )
		{
			$Twprofile = $this->Twprofile->find('first',
					array('conditions'=> 
						array('Twprofile.twitter_id' => $this->data['Twitterlogin']['twitter_id'])
						));
				
			if (!$Twprofile) //For some reason we couldnt find their twitter information, so they need to start over
			{
				$this->Session->setFlash(__('We lost your twitter tokens, please try again.', true), 'default', array('class' => 'error'));
				$loginPage = Router::url(array('plugin' => null, 'controller' => 'users', 'action' => 'login'), true);
			    header("Location: " . $loginPage);
			    exit();
			}
			
			$userEmailExists = $this->User->find('first',
					array('conditions'=> 
						array('User.email' => $this->data['Twitterlogin']['email'])
						));
			
			//user doesn't exist, so create one
			if ( !$userEmailExists )
			{
				$User = array();
				$User['User'] = array();
				$User['User']['username'] = $Twprofile['Twprofile']['username'];
				$User['User']['name'] = $Twprofile['Twprofile']['username'];
				$User['User']['email'] = $this->data['Twitterlogin']['email'];
				$User['User']['password'] = $Twprofile['Twprofile']['oauth_token'];
				$User['User']['status'] = 1;
				// @TODO need to let the administrator decide what the default role is to be.
				$User['User']['role_id'] = 2;
				
				$this->User->create();
				if ( $this->User->save( $User ) )
				{
					$User['User']['id'] = $Twprofile['Twprofile']['croogo_id'] = $this->User->id;
					$this->Twprofile->save( $Twprofile );
					
					//and this is an authenticated, logged in, happy user
					$this->Auth->fields = array('username' => 'id', 'password' => 'email'); 
					$this->Auth->autoRedirect = false; 
					$this->Auth->user( $User['User']['id'] );
					$this->Auth->login( $User['User'] );
					
					$this->Session->setFlash(__('All Done! You can now communicate with Twitter.', true), 'default', array('class' => 'success'));
					$homePage = Router::url($this->loginRedirect, true);
				    header("Location: " . $homePage);
				    exit();
				}
				
				// error saving the user
				$this->Session->setFlash(__('Error creating your account. Please try again.', true), 'default', array('class' => 'error'));
				$lastStep = Router::url(array('plugin' => null, 'controller' => 'twitterlogin', 'action' => 'laststep'), true);
				header("Location: " . $lastStep);
				exit();
			}
			else // the user DOES exist so tell them to log in
			{
				$this->Session->setFlash(__('That email is already used. Please login if you want to use that email.', true), 'default', array('class' => 'error'));
			}
		}
		
		
		// if the user is connecting their email
		elseif ( isset($this->data['Twitterlogin']['username']) && isset($this->data['Twitterlogin']['username']) )
		{
			// setting up the authentication
			$this->Auth->autoRedirect = false; 
			$this->Auth->fields = array('username' => 'username', 'password' => 'password'); 
			$this->data['Twitterlogin']['password'] = $this->Auth->password($this->data['Twitterlogin']['password']);
			
			if ( ! $authorized = $this->Auth->login( $this->data['Twitterlogin'] ) )
			{
				// failed to login, retry
				$this->Session->setFlash(__('Authorization Failed. Please try again.', true), 'default', array('class' => 'error'));
				$lastStep = Router::url(array('plugin' => null, 'controller' => 'twitterlogin', 'action' => 'laststep'), true);
				header("Location: " . $lastStep);
				exit();
			}
			
			$Twprofile = $this->Twprofile->find('first',
					array('conditions'=> 
						array('Twprofile.twitter_id' => $this->data['Twitterlogin']['twitter_id'])
						));
				
			if (!$Twprofile) //For some reason we couldnt find their twitter information, so they need to start over
			{
				$this->Session->setFlash(__('We lost your twitter tokens, please try again.', true), 'default', array('class' => 'error'));
				$loginPage = Router::url(array('plugin' => null, 'controller' => 'users', 'action' => 'login'), true);
			    header("Location: " . $loginPage);
			    exit();
			}
			
			$auth = $this->Session->read('Auth');
			$Twprofile['Twprofile']['croogo_id'] = $auth['User']['id'];
			$this->Twprofile->save( $Twprofile );
			
			// successfully connected
			$this->Session->setFlash(__('All Done! You can now communicate with Twitter.', true), 'default', array('class' => 'success'));
			$homePage = Router::url($this->loginRedirect, true);
			header("Location: " . $homePage);
			exit();
		}
	}
    
	/**
	 * Updating the administrative values
	 * 
	 * @return null
	 */
    public function admin_update()
    {
    	if( !$this->Twitterlogin->save( $this->data ) )
		{
    		$this->Session->setFlash(__('Something went wrong, we couldnt save to your db.', true), 'default', array('class' => 'error'));
    	}
    	else
    	{
    		$this->Session->setFlash(__('Your Twitter settings were successfully saved.', true), 'default', array('class' => 'success'));
    	}
    	
    	$url = Router::url(array('plugin' => null, 'controller' => 'twitterlogin', 'action' => 'admin_index'), true);
		header( "Location: " . $url );
		exit();
    }
    
    /**
     * The administrative area
     *
     */
    public function admin_index()
    {
    	$twitterlogin = $this->Twitterlogin->findById(1);
		$this->set(compact('twitterlogin'));
        $this->set('title_for_layout', __('Twitterlogin', true));
    }

    /**
     * The front end
     *
     */
    public function index()
    {
    	
    }
    
    /**
     * function is responsible for tweeting
     *
     * @return unknown
     */
    public function tweetit()
    {
    	//reasons to fail
    	if (!isset($this->params['url']['tweet'])) return false;
    	
    	$tweet = twitter();
    	$tweet->post('statuses/update', array('status' => $this->params['url']['tweet']));
    	die();
    }
    
    /**
     * function is responsible for marking a tweet as favorited
     *
     * @return unknown
     */
    public function favorite()
    {	
    	//reasons to fail
    	if (!isset($this->params['url']['id'])) return false;
    	
    	$tweet = twitter();
    	$r = $tweet->createFavorite( $this->params['url']['id'] );
    	die();
    }
}

