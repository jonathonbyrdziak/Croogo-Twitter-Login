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

//including stylesheets
$html->css('/twitterlogin/css/style.css', 'stylesheet', array('inline' => false));

?>
<div class="twitterlogin">
	<h2><?php __('Last step to connect your accounts.'); ?></h2>
	<?php echo $form->create('Twitterlogin', array('url' => array('plugin' => null, 'controller' => 'twitterlogin', 'action' => 'laststep')));?>
		<p><?php echo __('<b>Almost Finished!</b> Since this is your first login with twitter we need to collect some additional data from you. If you already have an account with us, please log in so that we can merge your accounts. If you do not currently have an account with us, please provide your email address so that we can create one.'); ?></p>
		
		<div class="twitterlogin-or"><h2><?php echo __('OR'); ?></h2></div>
		<div class="twitterlogin-left">
			<h2><?php echo __('Connect to Existing'); ?></h2>
			<?php
			echo $form->input('username');
			echo $form->input('password');
			?>
		</div>
		<div class="twitterlogin-right">
			<h2><?php echo __('Create New Account'); ?></h2>
			<?php echo $form->input('email'); ?>
		</div>
		
		<div class="clearfix"></div>
	<?php echo $form->input('twitter_id', array(
				        'value' => $twitter_id,
				        'type'  => 'hidden'
				));
	 ?>
	<?php echo $form->end('Submit');?>
	<div class="clearfix"></div>
</div>