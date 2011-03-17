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

/**
 * Schema for twitter db table
 *
 */
class TwitterloginsSchema extends CakeSchema
{

    var $name = 'Twitterlogins';

    function before($event = array()) {}
    function after($event = array()) {}

    var $twitterlogins = array(
        'id'				=> array('type' => 'integer',   'null' => false, 'default' => 1, 	'length' => 11, 'key' => 'primary'),
        'api_key'			=> array('type' => 'string',    'null' => false, 'default' => '',	'lenght' => 255),
        'consumer_key'		=> array('type' => 'string', 	'null' => false, 'default' => '',	'length' => 255),
        'consumer_secret'	=> array('type' => 'string',   	'null' => false, 'default' => '', 	'length' => 255),
        'request_token_url'	=> array('type' => 'string', 	'null' => false, 'default' => 'https://api.twitter.com/oauth/request_token', 	'length' => 255),
        'access_token_url'	=> array('type' => 'string', 	'null' => false, 'default' => 'https://api.twitter.com/oauth/access_token', 	'length' => 255),
        'authorize_url'		=> array('type' => 'string', 	'null' => false, 'default' => 'https://api.twitter.com/oauth/authorize', 	'length' => 255),
        'twitter_username'	=> array('type' => 'string', 	'null' => false, 'default' => '', 	'length' => 255),
        'twitter_password'	=> array('type' => 'string', 	'null' => false, 'default' => '', 	'length' => 255),
    );
}
