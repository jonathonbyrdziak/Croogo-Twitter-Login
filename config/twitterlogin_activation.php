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
class TwitterloginActivation {
/**
 * onActivate will be called if this returns true
 *
 * @param  object $controller Controller
 * @return boolean
 */
    public function beforeActivation(&$controller) {
        return true;
    }
/**
 * Called after activating the plugin in ExtensionsPluginsController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    public function onActivation(&$controller) {
        // ACL: set ACOs with permissions
        $controller->Croogo->addAco('Twitterlogin');
        $controller->Croogo->addAco('Twitterlogin/admin_index'); // TwitterloginController::admin_index()
        $controller->Croogo->addAco('Twitterlogin/admin_update');
        $controller->Croogo->addAco('Twitterlogin/callback', array('registered', 'public')); // TwitterloginController::index()
        $controller->Croogo->addAco('Twitterlogin/authorize', array('registered', 'public')); // TwitterloginController::index()
        $controller->Croogo->addAco('Twitterlogin/laststep', array('registered', 'public')); // TwitterloginController::index()
        $controller->Croogo->addAco('Twitterlogin/index', array('registered', 'public')); // TwitterloginController::index()
        
        // Main menu: add an Twitterlogin link
        $mainMenu = $controller->Link->Menu->findByAlias('main');
        $controller->Link->Behaviors->attach('Tree', array(
            'scope' => array(
                'Link.menu_id' => $mainMenu['Menu']['id'],
            ),
        ));
        $controller->Link->save(array(
            'menu_id' => $mainMenu['Menu']['id'],
            'title' => 'Twitterlogin',
            'link' => 'plugin:twitterlogin/controller:twitterlogin/action:index',
            'status' => 1,
        ));
        
        //Don't forget to run our other installation procedures!
        $this->createDatabaseSchemas($controller);
    }
/**
 * onDeactivate will be called if this returns true
 *
 * @param  object $controller Controller
 * @return boolean
 */
    public function beforeDeactivation(&$controller) {
        return true;
    }
/**
 * Called after deactivating the plugin in ExtensionsPluginsController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    public function onDeactivation(&$controller) {
        // ACL: remove ACOs with permissions
        $controller->Croogo->removeAco('Twitterlogin'); // TwitterloginController ACO and it's actions will be removed

        // Main menu: delete Twitterlogin link
        $link = $controller->Link->find('first', array(
            'conditions' => array(
                'Menu.alias' => 'main',
                'Link.link' => 'plugin:twitterlogin/controller:twitterlogin/action:index',
            ),
        ));
        $controller->Link->Behaviors->attach('Tree', array(
            'scope' => array(
                'Link.menu_id' => $link['Link']['menu_id'],
            ),
        ));
        if (isset($link['Link']['id'])) {
            $controller->Link->delete($link['Link']['id']);
        }
    }
/**
 * Create the database scheme for our twitter login model
 *
 * @param unknown_type $controller
 */
    public function createDatabaseSchemas(&$controller)
    {
		App::Import('CakeSchema');
        $CakeSchema = new CakeSchema();
        $db =& ConnectionManager::getDataSource('default');

        $schema_files = array(
            'twitterlogins.php',
        	'twprofiles.php'
        );
        
        foreach($schema_files as $schema_file) {
        	$class_name = Inflector::camelize(substr($schema_file, 0, -4)).'Schema';
        	$table_name = substr($schema_file, 0, -4);

        	if(!in_array($table_name, $db->_sources)) {
        		require_once dirname(__file__).DS.'schema'.DS.$schema_file;
	        	$ActivateSchema = new $class_name;
	        	$created = false;
				if(isset($ActivateSchema->tables[$table_name])) {
					$db->execute($db->createSchema($ActivateSchema, $table_name));
				}
			}
        }
    }
}
?>