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
 * Routes
 *
 * twitterlogin_routes.php will be loaded in main app/config/routes.php file.
 */
    Croogo::hookRoutes('Twitterlogin');
/**
 * Behavior
 *
 * This plugin's Example behavior will be attached whenever Node model is loaded.
 */
    //Croogo::hookBehavior('Node', 'Example.Example', array());
/**
 * Component
 *
 * This plugin's Example component will be loaded in ALL controllers.
 */
    Croogo::hookComponent('*', 'Twitterlogin.Twitterlogin');
/**
 * Helper
 *
 * This plugin's Twitterlogin helper will be loaded via UsersController.
 */
    //Croogo::hookHelper('Users', 'Twitterlogin.Twitterlogin');
/**
 * Admin menu (navigation)
 *
 * This plugin's admin_menu element will be rendered in admin panel under Extensions menu.
 */
    Croogo::hookAdminMenu('Twitterlogin');
/**
 * Admin row action
 *
 * When browsing the content list in admin panel (Content > List),
 * an extra link called 'Example' will be placed under 'Actions' column.
 */
    //Croogo::hookAdminRowAction('Twitterlogin/admin_index', 'Twitter Login', 'plugin:twitterlogin/controller:twitterlogin/action:index/:id');
/**
 * Admin tab
 *
 * When adding/editing Content (Nodes),
 * an extra tab with title 'Example' will be shown with markup generated from the plugin's admin_tab_node element.
 *
 * Useful for adding form extra form fields if necessary.
 */
    //Croogo::hookAdminTab('Nodes/admin_add', 'Example', 'example.admin_tab_node');
    //Croogo::hookAdminTab('Nodes/admin_edit', 'Example', 'example.admin_tab_node');
    
?>