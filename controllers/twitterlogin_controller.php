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
	public $components = array('Security','Twitterlogin.Abraham','Session');
    
	/**
	 * Models used by the Controller
	 *
	 * @var array
	 * @access public
	 */
	public $uses = array('Twitterlogin','User','Twprofile');
	
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
        
        //loading resources
    	global $twConfigs;
    	$twConfigs = $this->Twitterlogin->findById(1);
    	$twConfigs = $twConfigs['Twitterlogin'];
    	
    }
    
    /**
     * the administrative area
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
        //$this->set('title_for_layout', __('Twitterlogin', true));
    }
	
    /**
     * Twitter calls this back
     * 
     * @link http://www.yourdomain.com/twitterlogin/callback
     */
    public function callback( $type = 'twitter' )
    {
    	//loading resources
    	global $twConfigs;
		
		// If the oauth_token is old redirect to the connect page.
		if ($this->Session->read('Twitter.oauth_token') !== $this->params['url']['oauth_token'])
		{
			$this->Session->write('Twitter.oauth_status', 'oldtoken');
			$this->Session->destroy();
			
			$this->Session->setFlash(__('Sorry, we had to clear your cache. Please try again.', true), 'default', array('class' => 'error'));
			$loginPage = Router::url(array('plugin' => null, 'controller' => 'users', 'action' => 'login'), true);
		    header("Location: " . $loginPage);
		    exit();
		}
		
		// Create TwitteroAuth object with app key/secret and token key/secret from default phase
		$this->Abraham->initialize($twConfigs['consumer_key'], $twConfigs['consumer_secret'], $this->Session->read('Twitter.oauth_token'), $this->Session->read('Twitter.oauth_token_secret'));
		
		// Request access tokens from twitter
		$access_token = $this->Abraham->getAccessToken($this->params['url']['oauth_verifier']);
		
		// Save the access tokens. Normally these would be saved in a database for future use.
		//@TODO Save these access tokens to the database
		//@TODO Create the user or attach the user to this account
		$this->Session->write('access_token', $access_token);
		
		$data = array();
		$data['Twprofile'] = array();
		$data['Twprofile']['twitter_id'] = $access_token['user_id'];
		$data['Twprofile']['oauth_token'] = $access_token['oauth_token'];
		$data['Twprofile']['oauth_token_secret'] = $access_token['oauth_token_secret'];
		$data['Twprofile']['username'] = $access_token['screen_name'];
		
		$this->Twprofile->create( );
		if ( $this->Twprofile->save( $data ) )
		{
			$this->Session->write('Twitter.id', $this->Twprofile->id);
		}
		
		// Remove no longer needed request tokens 
		$this->Session->delete('Twitter.oauth_token');
		$this->Session->delete('Twitter.oauth_token_secret');
		
		// If HTTP response is 200 continue otherwise send to connect page to retry
		if (200 == $this->Abraham->http_code)
		{
			// The user has been verified and the access tokens can be saved for future use
			$this->Session->write('Twitter.oauth_status', 'verified');
			
			$lastStep = Router::url(array('plugin' => null, 'controller' => 'twitterlogin', 'action' => 'laststep'), true);
		    header("Location: " . $lastStep);
		}
		else
		{
			// Save HTTP status for error dialog on connnect page.
			$this->Session->write('Twitter.oauth_status', 'oldtoken');
			$this->Session->destroy();
			
			$this->Session->setFlash(__('Sorry, we had to clear your cache. Please try again.', true), 'default', array('class' => 'error'));
			$loginPage = Router::url(array('plugin' => null, 'controller' => 'users', 'action' => 'login'), true);
		    header("Location: " . $loginPage);
		}
		exit();
    }

	/**
	 * OAUTH function that redirects to authorize URL.
	 *
	 * @param unknown_type $type
	 */
	function authorize( $type = 'twitter' )
	{
		//loading resources
    	global $twConfigs;
    	
		// Build TwitterOAuth object with client credentials.
		$this->Abraham->initialize($twConfigs['consumer_key'], $twConfigs['consumer_secret']);
		
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
	 * This step asks the user for his email address.
	 *
	 * @return null
	 */
	public function laststep()
	{
		
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
}
?>