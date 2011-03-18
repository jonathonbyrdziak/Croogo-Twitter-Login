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

CroogoRouter::connect('/twitterlogin/callback', array('plugin' => 'twitterlogin', 'controller' => 'twitterlogin', 'action' => 'callback'));
CroogoRouter::connect('/twitterlogin/authorize', array('plugin' => 'twitterlogin', 'controller' => 'twitterlogin', 'action' => 'authorize'));
CroogoRouter::connect('/twitterlogin/laststep', array('plugin' => 'twitterlogin', 'controller' => 'twitterlogin', 'action' => 'laststep'));
