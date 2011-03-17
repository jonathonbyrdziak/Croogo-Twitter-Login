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
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    public $uses = array('Setting');

    public function admin_index() {
        $this->set('title_for_layout', __('Twitterlogin', true));
    }

    public function index() {
        $this->set('title_for_layout', __('Twitterlogin', true));
        $this->set('twitterloginVariable', 'value here');
    }

}
?>