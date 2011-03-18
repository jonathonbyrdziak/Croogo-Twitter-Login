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
class TwitterloginHelper extends AppHelper {
/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
    public $helpers = array(
        'Html',
        'Layout',
    );
/**
 * Before render callback. Called before the view file is rendered.
 *
 * @return void
 */
    public function beforeRender() {
    }
/**
 * After render callback. Called after the view file is rendered
 * but before the layout has been rendered.
 *
 * @return void
 */
    public function afterRender() {
    }
/**
 * Before layout callback. Called before the layout is rendered.
 *
 * @return void
 */
    public function beforeLayout() {
    }
/**
 * After layout callback. Called after the layout has rendered.
 *
 * @return void
 */
    public function afterLayout() {
    }
/**
 * Called after LayoutHelper::setNode()
 *
 * @return void
 */
    public function afterSetNode() {
        // field values can be changed from hooks
        $this->Layout->setNodeField('title', $this->Layout->node('title') . ' [Modified by TwitterloginHelper]');
    }
/**
 * Called before LayoutHelper::nodeInfo()
 *
 * @return string
 */
    public function beforeNodeInfo() {
        //return '<p>beforeNodeInfo</p>';
    }
/**
 * Called after LayoutHelper::nodeInfo()
 *
 * @return string
 */
    public function afterNodeInfo() {
        //return '<p>afterNodeInfo</p>';
    }
/**
 * Called before LayoutHelper::nodeBody()
 *
 * @return string
 */
    public function beforeNodeBody() {
        //return '<p>beforeNodeBody</p>';
    }
/**
 * Called after LayoutHelper::nodeBody()
 *
 * @return string
 */
    public function afterNodeBody() {
        //return '<p>afterNodeBody</p>';
    }
/**
 * Called before LayoutHelper::nodeMoreInfo()
 *
 * @return string
 */
    public function beforeNodeMoreInfo() {
        //return '<p>beforeNodeMoreInfo</p>';
    }
/**
 * Called after LayoutHelper::nodeMoreInfo()
 *
 * @return string
 */
    public function afterNodeMoreInfo() {
        //return '<p>afterNodeMoreInfo</p>';
    }
}

/**
 * Template code for including the twitter login button into the theme
 *
 * @param boolean $display_if_logged_in
 * @return html|string
 */
function twitter_login_button( $display_if_logged_in = false )
{
	// The same as require('controllers/users_controller.php');
	App::import('Helper', 'Html');
	$html = new HtmlHelper;
	
	$html->css('/twitterlogin/css/style.css', 'stylesheet', array('inline' => false));
	
	echo "<a href='".
	Router::url(array('admin' => false, 'plugin' => null, 'controller' => 'twitterlogin', 'action' => 'authorize'), true)
	."'><div class='twitter-login-lighter'></div></a>";
	
}

