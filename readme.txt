Installation
--------------------------------------------------
After activation head to the Plugins >> Twitter Login >> Settings page and follow the steps to register your application.
Once you've registered your application you need to save it in the settings area.
Finally there are some html helpers that you'll need to include in your template, so open your components/users_controller.php and include the following lines in your UserController

    public $helpers = array(
    	'Twitterlogin.Twitterlogin'
    );
    
Obviously you cannot declare the $helpers member twice, so make sure you merge this with your current code properly.

Template Codes
--------------------------------------------------
Now, the template code that you're going to take advantage of is listed below. This code will display the twitter login button where ever you place it.

	twitter_login_button( $display_if_logged_in = false );
	
	//regular usage is something like this
	if (function_exists('twitter_login_button')) twitter_login_button();

The $display_if_logged_in parameter will force the system to display the twitter login button, even if the user is already logged in. Note: This confuses a lot of people if you don't use it in the right location.

I suggest that first place this template code into your admin login page to get things working before you use it on your homepage.
