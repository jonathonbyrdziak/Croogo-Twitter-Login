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
class TwitterloginBehavior extends ModelBehavior {
/**
 * Setup
 *
 * @param object $model
 * @param array  $config
 * @return void
 */
    public function setup(&$model, $config = array()) {
        if (is_string($config)) {
            $config = array($config);
        }

        $this->settings[$model->alias] = $config;
    }
/**
 * afterFind callback
 *
 * @param object  $model
 * @param array   $created
 * @param boolean $primary
 * @return array
 */
    public function afterFind(&$model, $results = array(), $primary = false) {
        if ($primary && isset($results[0][$model->alias])) {
            foreach ($results AS $i => $result) {
                if (isset($results[$i][$model->alias]['body'])) {
                    $results[$i][$model->alias]['body'] .= '<p>[Modified by TwitterloginBehavior]</p>';
                }
            }
        } elseif (isset($results[$model->alias])) {
            if (isset($results[$model->alias]['body'])) {
                $results[$model->alias]['body'] .= '<p>[Modified by TwitterloginBehavior]</p>';
            }
        }

        return $results;
    }

}
?>