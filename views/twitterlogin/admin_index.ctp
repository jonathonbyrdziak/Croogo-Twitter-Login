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
 * @link	 http://www.5twentystudios.com
 * @repository https://github.com/Jonathonbyrd/Croogo-Twitter-Login
 */

//including stylesheets
$html->css('/twitterlogin/css/style.css', 'stylesheet', array('inline' => false));

?>
<div class="twitteradmin index">
	<h2><?php echo $title_for_layout; ?></h2>
	<p><?php __('Creating a twitter login is not too difficult. I will try my best to alleviate the pain with this plugin, but I do not guarantee that this is going to go smoothly for you.'); ?></p>
	<br/>
	<p><?php __('Step One: Create an accout for your new website <a href="https://twitter.com/signup" target="_blank">via Twitter signup</a>.'); ?></p>
	<p><?php __('Step Two: Head on over to the <a href="http://dev.twitter.com/" target="_blank">Twitter Developers</a> area.'); ?></p>
	<p><?php __('Step Three: Register your application, heres your callback url: '); echo Router::url(array('admin' => false, 'plugin' => 'twitterlogin', 'controller' => 'twitterlogin', 'action' => 'callback'), true); ?></p>
	<p><?php __('Step Four: Once registered, twitter is going to give you back a lot of data, just go ahead and enter all of that here.'); ?></p>
	<br/>
	
	<?php 
	if (!twitter('profile.id',null,false) && $twitterlogin['Twitterlogin']['consumer_key'] && $twitterlogin['Twitterlogin']['consumer_secret']):
		__('<p>Test it out. Merge your account!</p>'); 
		twitter_login_button();
	
	elseif (twitter()): 
		$twitter = twitter();
		?>
		<h2>You're successfully connected to Twitter, LIVE!</h2>
		<p><?php __("I've made this as simple as I can figure for most people. If you want the entire object back, simply use the following code."); ?></p>
<code>$twitter = twitter();</code>
		
		<p><?php __("I've built in a seemless retweet option, here's how to retweet something from within your code."); ?></p>
<code>$twitter = twitter();
$twitter->post('statuses/update', array('status' => 'Just testing out my new retweeter plugin. http://www.jonathonbyrd.com'));
</code>
		
		<p><?php __("Or, you can ask twitter for any of the following data:"); ?></p>
		
		<table style="width:100%">
			<tr>
				<td width="200px">Twitter ID:</td>
				<td><?php twitter('profile.id'); ?></td>
				<td>twitter('profile.id');</td>
			</tr>
			<tr>
				<td width="200px">Name:</td>
				<td><?php twitter('profile.name'); ?></td>
				<td>twitter('profile.name');</td>
			</tr>
			<tr>
				<td width="200px">Username:</td>
				<td><?php twitter('profile.screen_name'); ?></td>
				<td>twitter('profile.screen_name');</td>
			</tr>
			<tr>
				<td width="200px">Description:</td>
				<td><?php twitter('profile.description'); ?></td>
				<td>twitter('profile.description');</td>
			</tr>
			<tr>
				<td width="200px">Website:</td>
				<td><?php twitter('profile.url'); ?></td>
				<td>twitter('profile.url');</td>
			</tr>
			<tr>
				<td width="200px">Location:</td>
				<td><?php twitter('profile.location'); ?></td>
				<td>twitter('profile.location');</td>
			</tr>
			<tr>
				<td width="200px">Time Zone:</td>
				<td><?php twitter('profile.time_zone'); ?></td>
				<td>twitter('profile.time_zone');</td>
			</tr>
			
			
			<tr>
				<td width="200px">Tweets:</td>
				<td><?php twitter('profile.statuses_count'); ?></td>
				<td>twitter('profile.statuses_count');</td>
			</tr>
			<tr>
				<td width="200px">Friends:</td>
				<td><?php twitter('profile.friends_count'); ?></td>
				<td>twitter('profile.friends_count');</td>
			</tr>
			<tr>
				<td width="200px">Following Requests:</td>
				<td><?php twitter('profile.follow_request_sent'); ?></td>
				<td style="white-space:nowrap;">twitter('profile.follow_request_sent');</td>
			</tr>
			<tr>
				<td width="200px">Favourites Count:</td>
				<td><?php twitter('profile.favourites_count'); ?></td>
				<td>twitter('profile.favourites_count');</td>
			</tr>
			<tr>
				<td width="200px">Followers:</td>
				<td><?php twitter('profile.followers_count'); ?></td>
				<td>twitter('profile.followers_count');</td>
			</tr>
			
			
			<tr>
				<td width="200px">Current Status:</td>
				<td><?php twitter('status.text'); ?></td>
				<td>twitter('status.text');</td>
			</tr>
			<tr>
				<td width="200px">Status ID:</td>
				<td><?php twitter('status.id'); ?></td>
				<td>twitter('status.id');</td>
			</tr>
			<tr>
				<td width="200px">Source:</td>
				<td><?php twitter('status.source'); ?></td>
				<td>twitter('status.source');</td>
			</tr>
			<tr>
				<td width="200px">Date:</td>
				<td><?php twitter('status.created_at'); ?></td>
				<td>twitter('status.created_at');</td>
			</tr>
			
		</table>
	<?php endif; ?>
	
	
	<?php echo $form->create(null, array('url' => array('plugin' => 'twitterlogin', 'controller' => 'Twitterlogin', 'action' => 'update'))); ?>
		<?php
		echo $form->input('Twitterlogin.id', array(
						'value' => 1,
						'type'  => 'hidden'
				));

		echo $form->input('Twitterlogin.api_key', array(
						'label' => __('API Key',true),
						'type'  => 'text',
						//'class' => 'required',
						'style' => 'width:75%;',
						'value' => $twitterlogin['Twitterlogin']['api_key']
				));

		echo $form->input('Twitterlogin.consumer_key', array(
						'label' => __('Consumer Key',true),
						'type'  => 'text',
						'style' => 'width:75%;',
						'value' => $twitterlogin['Twitterlogin']['consumer_key']
				));
				
		echo $form->input('Twitterlogin.consumer_secret', array(
						'label' => __('Consumer Secret',true),
						'type'  => 'text',
						'style' => 'width:75%;',
						'value' => $twitterlogin['Twitterlogin']['consumer_secret']
				));
		
		echo $form->input('Twitterlogin.request_token_url', array(
						'label' => __('Request Token URL',true),
						'type'  => 'text',
						'style' => 'width:75%;',
						'value' => ($twitterlogin['Twitterlogin']['request_token_url'])? $twitterlogin['Twitterlogin']['request_token_url']: 'https://api.twitter.com/oauth/request_token',
				));
				
		echo $form->input('Twitterlogin.access_token_url', array(
						'label' => __('Access Token URL',true),
						'type'  => 'text',
						'style' => 'width:75%;',
						'value' => ($twitterlogin['Twitterlogin']['access_token_url'])? $twitterlogin['Twitterlogin']['access_token_url']: 'https://api.twitter.com/oauth/access_token',
				));
				
		echo $form->input('Twitterlogin.authorize_url', array(
						'label' => __('Authorize URL',true),
						'type'  => 'text',
						'style' => 'width:75%;',
						'value' => ($twitterlogin['Twitterlogin']['authorize_url'])? $twitterlogin['Twitterlogin']['authorize_url']: 'https://api.twitter.com/oauth/authorize',
				));
				
		echo $form->input('Twitterlogin.twitter_username', array(
						'label' => __('Your Twitter Username',true),
						'type'  => 'text',
						'style' => 'width:75%;',
						'value' => $twitterlogin['Twitterlogin']['twitter_username']
				));
				
		echo $form->input('Twitterlogin.twitter_password', array(
						'label' => __('Your Twitter Password',true),
						'type'  => 'password',
						'style' => 'width:75%;',
						'value' => $twitterlogin['Twitterlogin']['twitter_password']
				));
		
		?>
	<?php echo $form->submit('Update');?>
	<?php echo $form->end();?>
</div>