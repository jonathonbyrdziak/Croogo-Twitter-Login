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

?>
<div class="users form">
    <h2><?php __('Last step to, connect your accounts.'); ?></h2>
    <?php echo $form->create('Twitterlogin', array('url' => array('plugin' => null, 'controller' => 'twitterlogin', 'action' => 'laststep')));?>
        <p><b>Almost Finished!</b> We just need to connect your twitter account to this account.</p>
        <fieldset>
        <?php
        
            echo $form->input('username');
            echo $form->input('password');
            
            echo $form->input('email');
            
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>