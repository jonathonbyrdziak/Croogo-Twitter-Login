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
class TwprofilesSchema extends CakeSchema
{

    var $name = 'Twprofiles';

    function before($event = array()) {}
    function after($event = array()) {}

    var $twprofiles = array(
        'id'				=> array('type' => 'integer',   'null' => false, 'default' => 1, 	'length' => 11, 'key' => 'primary'),
        'croogo_id'			=> array('type' => 'string',    'null' => false, 'default' => '',	'lenght' => 255),
        'twitter_id'		=> array('type' => 'string',    'null' => false, 'default' => '',	'lenght' => 255),
        'oauth_token'		=> array('type' => 'string',    'null' => false, 'default' => '',	'lenght' => 255),
        'oauth_token_secret'=> array('type' => 'string',    'null' => false, 'default' => '',	'lenght' => 255),
        'username'			=> array('type' => 'string', 	'null' => false, 'default' => '',	'length' => 255),
        'avatar'			=> array('type' => 'string',   	'null' => false, 'default' => '', 	'length' => 255),
    );
}
