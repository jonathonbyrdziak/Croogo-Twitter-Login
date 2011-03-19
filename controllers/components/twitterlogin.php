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
class TwitterloginComponent extends Object 
{
	/**
	 * Called after the Controller::beforeFilter() and before the controller action
	 *
	 * @param object $controller Controller with components to startup
	 * @return void
	 */
    public function startup(&$controller)
    {
		$controller->set('twitterloginComponent', 'TwitterloginComponent startup');
	    
		App::import('Component', 'Abraham');
		if (class_exists('AbrahamComponent'))
		{
			// loading resources
			$Abraham = new AbrahamComponent;
		}
    }
    
	/**
	 * Called after the Controller::beforeRender(), after the view class is loaded, and before the
	 * Controller::render()
	 *
	 * @param object $controller Controller with components to beforeRender
	 * @return void
	 */
    public function beforeRender(&$controller) 
    {
    	
    }
    
	/**
	 * Called after Controller::render() and before the output is printed to the browser.
	 *
	 * @param object $controller Controller with components to shutdown
	 * @return void
	 */
    public function shutdown(&$controller) 
    {
    	
    }
    
}

/**
 * Returns the current url
 *
 * @return unknown
 */
function twitter_redirect_url()
{
	$pageURL = 'http';
	if (@$_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	
	//initializing variables
	App::import('Helper', 'Session');
	$Session = new SessionComponent;
	$pageURL = Router::url($pageURL, true);
	
	//$url = urlencode($pageURL);
	$loginPage = Router::url(array('admin' => false, 'plugin' => null, 'controller' => 'users', 'action' => 'login'), true);
	if ($loginPage == $pageURL) return false;
	
	$loginPage = Router::url(array('admin' => true, 'controller' => 'users', 'action' => 'login'), true);
	if ($loginPage == $pageURL) return false;
	
	//$Session->write("Twitter.redirect", $pageURL);
}

//remember the last url the user saw
twitter_redirect_url();

/**
 * Template code for including the twitter login button into the theme
 *
 * @param boolean $display_if_logged_in
 * @return html|string
 */
function twitter_login_button( $display_if_logged_in = true )
{
	//initializing variables
	App::import('Helper', 'Session');
	$Session = new SessionComponent;
	$auth = $Session->read('Auth');
	
	//reasons to fail
	if (twitter_connected()) return false;
	if (!$display_if_logged_in && isset($auth['User']['id'])) return false;
	
	// The same as require('controllers/users_controller.php');
	App::import('Helper', 'Html');
	$html = new HtmlHelper;
	
	$html->css('/twitterlogin/css/style.css', 'stylesheet', array('inline' => false));
	
	echo "<a href='".
	Router::url(array('admin' => false, 'plugin' => null, 'controller' => 'twitterlogin', 'action' => 'authorize'), true)
	."'><div class='twitter-login-lighter'></div></a>";
	
}

/**
 * Function will return a global reference twitter object
 *
 * @param unknown_type $variable
 * @param unknown_type $user_id
 * @param unknown_type $echo
 * @return unknown
 */
function &twitter( $variable = null, $user_id = null, $echo = true )
{
	// initializing variables
	global $Abraham;
	
	// reasons to fail
	if (!isset($Abraham) || !$Abraham) return false;
	
	if ( !is_null($variable) )
	{
		if ($echo)
			echo $Abraham->get($variable);
		else
			return $Abraham->get($variable);
	}
	
    return $Abraham;
}

/**
 * Function to determine if the user is connected to twitter
 *	
 * @return boolean
 */
function twitter_connected()
{
	// initializing variables
	global $Abraham;
	
	// reasons to fail
	if ( !isset($Abraham) || !$Abraham ) return false;
	if ( !$Abraham->authorized ) return false;
	
	return true;
}

