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
?><div class="Twitterlogin index">
    <h2><?php echo $title_for_layout; ?></h2>
    <p><?php __('Creating a twitter login is not too difficult. I will try my best to alleviate the pain with this plugin, but I do not guarantee that this is going to go smoothly for you.'); ?></p>
    <br/>
    <p><?php __('Step One: Create an accout for your new website <a href="https://twitter.com/signup" target="_blank">via Twitter signup</a>.'); ?></p>
    <p><?php __('Step Two: Head on over to the <a href="http://dev.twitter.com/" target="_blank">Twitter Developers</a> area.'); ?></p>
    <p><?php __('Step Three: Register your application, heres your callback url: '); echo Router::url(array('admin' => false, 'plugin' => 'twitterlogin', 'controller' => 'twitterlogin', 'action' => 'callback'), true); ?></p>
    <p><?php __('Step Four: Once registered, twitter is going to give you back a lot of data, just go ahead and enter all of that here.'); ?></p>
    <br/>
    
    <?php echo $form->create(null, array('url' => array('plugin' => 'twitterlogin', 'controller' => 'twitterlogin', 'action' => 'update'))); ?>
		<?php
		echo $form->input('Twitterlogin.id', array(
				        'value' => 1,
				        'type'  => 'hidden'
				));

		echo $form->input('Twitterlogin.api_key', array(
						'label' => __('API Key',true),
				        'type'  => 'text',
				        //'class' => 'required',
				        'value' => $twitterlogin['twitterlogin']['api_key']
				));

		echo $form->input('Twitterlogin.consumer_key', array(
				        'label' => __('Consumer Key',true),
				        'type'  => 'text',
				        'value' => $twitterlogin['twitterlogin']['consumer_key']
				));

		?>
    <?php echo $form->submit('Update');?>
    <?php echo $form->end();?>
</div>