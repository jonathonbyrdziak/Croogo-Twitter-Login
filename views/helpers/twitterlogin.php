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
class TwitterloginHelper extends AppHelper {
/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
    public $helpers = array(
        'Html',
        'Layout',
    );
/**
 * Before render callback. Called before the view file is rendered.
 *
 * @return void
 */
    public function beforeRender() 
    {
    	if (isset($this->params['admin'])) return false;
    	if (!twitter('profile.id',null,false)) return false;
    	
		//including stylesheets
		$this->Html->css('/twitterlogin/css/style.css', 'stylesheet', array('inline' => false));
    	echo $this->Html->scriptBlock("var twitterLogin = {
    	tweetit: '".Router::url(array('plugin' => null, 'controller' => 'twitterlogin', 'action' => 'tweetit'),true)."',
    	favorite: '".Router::url(array('plugin' => null, 'controller' => 'twitterlogin', 'action' => 'favorite'),true)."',
    	url: '".substr($u = Router::url('/',true), 0, strlen($u)-1)."',
    	username: '".twitter('profile.screen_name', null, false)."',
    	userid: '".twitter('profile.id', null, false)."'};");
    }
/**
 * After render callback. Called after the view file is rendered
 * but before the layout has been rendered.
 *
 * @return void
 */
    public function afterRender() 
    {
    	if (isset($this->params['admin'])) return false;
    	if (!twitter('profile.id',null,false)) return false;
    	
    	echo $this->Html->script(array('/twitterlogin/js/twitterlogin.js','/twitterlogin/js/jquery-1.5.1.min.js','/twitterlogin/js/jquery-ui-1.8.11.custom.min.js'));
    }
/**
 * Before layout callback. Called before the layout is rendered.
 *
 * @return void
 */
    public function beforeLayout() 
    {
    	
    }
/**
 * After layout callback. Called after the layout has rendered.
 *
 * @return void
 */
    public function afterLayout() 
    {
    	
    }
/**
 * Called after LayoutHelper::setNode()
 *
 * @return void
 */
    public function afterSetNode() 
    {
        // field values can be changed from hooks
        //$this->Layout->setNodeField('title', $this->Layout->node('title') . ' [Modified by TwitterloginHelper]');
    }
/**
 * Called before LayoutHelper::nodeInfo()
 *
 * @return string
 */
    public function beforeNodeInfo() 
    {
        //return '<p>beforeNodeInfo</p>';
    }
/**
 * Called after LayoutHelper::nodeInfo()
 *
 * @return string
 */
    public function afterNodeInfo() 
    {
        //return '<p>afterNodeInfo</p>';
    }
/**
 * Called before LayoutHelper::nodeBody()
 *
 * @return string
 */
    public function beforeNodeBody() 
    {
        //return '<p>beforeNodeBody</p>';
    }
/**
 * Called after LayoutHelper::nodeBody()
 *
 * @return string
 */
    public function afterNodeBody() 
    {
    	//reasons to fail
    	if (!twitter('profile.id',null,false)) return false;
    	
    	//initializing variables
    	static $favorites;
    	$favored = 'needs-favored';
    	$metas = array(
    		'tweet_id' => false
    	);
    	foreach ((array)$this->Layout->node['Meta'] as $meta)
    	{
    		$metas[$meta['key']] = $meta['value'];
    	}
    	if (!isset($favorites))
    	{
    		$tweet = twitter();
    		$favorites = $tweet->getFavorites();
    		//this method requires authentication and I can't figure it out
    		//print_r($favorites);
    	}
    	
    	if (false) //just need to check to see if the item is favored already here
    	{
    		$favored = 'tweet-favored';
    	}
    	
    	$html = 
    	'<span class="tweet-actions '.$favored.'" tweet_id="'.$metas['tweet_id'].'" data="'.base64_encode( json_encode($this->Layout->node['Node']) ).'">';
           
    	if ($metas['tweet_id'])
    	{
    		$html .= '<a href="#" class="favorite-action" title="Favorite">
            	<span><i></i><b>Favorite</b></span>
            </a>';
    	}
    	
		$html .= '<a href="#" class="reply-action" title="Reply">
            	<span><i></i><b>Reply</b></span>
            </a>
            <a href="#" class="retweet-action" title="Retweet">
            	<span><i></i><b>Retweet</b></span>
            </a>
    	</span>';
		
		return $html;
    }
/**
 * Called before LayoutHelper::nodeMoreInfo()
 *
 * @return string
 */
    public function beforeNodeMoreInfo() 
    {
        //return '<p>beforeNodeMoreInfo</p>';
    }
/**
 * Called after LayoutHelper::nodeMoreInfo()
 *
 * @return string
 */
    public function afterNodeMoreInfo() 
    {
        //return '<p>afterNodeMoreInfo</p>';
    }
}
?>