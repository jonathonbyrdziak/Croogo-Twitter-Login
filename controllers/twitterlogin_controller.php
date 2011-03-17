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
class TwitterloginController extends TwitterloginAppController {
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
    public $components = array('Security');
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    public $uses = array('Twitterlogin');

    public function admin_index() {
    	 $twitterlogin = $this->Twitterlogin->findById(1);

        if(!$twitterlogin){
        	//$this->Session->setFlash(__("We couldn't load your previous settings from the database", true), 'default', array('class' => 'error'));
        }

        $this->set(compact('twitterlogin'));
        $this->set('title_for_layout', __('Twitterlogin', true));
    }

    public function index() {
        $this->set('title_for_layout', __('Twitterlogin', true));
        $this->set('twitterloginVariable', 'value here');
    }
	
    /**
     * Twitter calls this back
     * 
     * @link http://www.swagaways.com/twitterlogin/callback
     */
    public function callback()
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