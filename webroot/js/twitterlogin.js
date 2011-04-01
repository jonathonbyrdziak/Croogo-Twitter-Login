/**
 * 5Twenty Studios
 * Twitter Login
 *
 * This jquery file handles all of the retweet abilities within the twitterlogin
 * extensions funcitonality. Although it does not handle the actual retweeting
 * it does handle the UI and communication involved with your own website.
 *
 * @package Twitter Login
 * @subpackage Croogo
 * @author   Jonathon Byrd <support@5twentystudios.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.5twentystudios.com
 * @repository https://github.com/Jonathonbyrd/Croogo-Twitter-Login
 * 
 */

/**
 * Retweeter
 * 
 * Javascript class to contain the api involved in the twitter retweet.
 * http://www.swagaways.com/twitterlogin/tweetit
 */
jQuery.noConflict();
var tweet;

(function($) {
	var api = tweet = {
		//options
		once : false,
		dialog_ele : {},
		url: '',
		
		//initialization funciton
		init: function()
		{
			//fail if we don't have the dialog plugin for jquery
			if (typeof($.fn.dialog) == 'undefined') return false;
			if (api.once) return false;
			api.once = true;

			api.url = twitterLogin.url;
			api.favorite = twitterLogin.favorite;
			api.tweetit = twitterLogin.tweetit;
			api.userid = twitterLogin.userid;
			api.username = twitterLogin.username;
			
			//placing necessary styles
			this.addClasses();

			//starting the event listeners
			this.startFavoriteListener();
			this.startReplyListener();
			this.startRetweetListener();
		},
		
		//mark a tweet as favored
		startFavoriteListener : function()
		{
			$('.needs-favored').find('.favorite-action').click(function(){
				//creating the retweet listener
				var favorite = $(this);
				var tweet_actions = $(this).parent();
				var tweet_id = tweet_actions.attr('tweet_id');
				
				$.ajax({
					method: 'GET',
					data:
					({
						id : tweet_id
					}),
					url: api.favorite,
					success : function(r)
					{
						tweet_actions.removeClass('needs-favored');
						tweet_actions.addClass('tweet-favored');
						favorite.unbind('click');
					}
				});
				
			});
		},
		
		//displays a reply
		startReplyListener : function()
		{
			$('.reply-action').click(function(){
				
				var base64 = $.base64Decode( $(this).parent().attr('data') );
				var data = $.parseJSON( base64 );
				
				//creates the popup inner html
				api.dialog_ele = $( 
				'<div>'+
					'<div class="twttr-dialog-body clearfix">'+
						'<div class="twttr-dialog-content">'+
							'<div class="tweet-box">'+
								'<div class="text-area">'+
									'<div class="text-area-editor twttr-editor">'+
										'<textarea class="twitter-anywhere-tweet-box-editor" style="width: 100%; height: 80px; ">@swagaways</textarea>'+
									'</div>'+
								'</div>'+
							'</div>'+
						'</div>'+
					'</div>'+
					
					'<div class="twttr-dialog-footer clearfix">'+
						'<div class="retweet twttr-dialog-reply-footer">'+
							'<img src="http://a1.twimg.com/sticky/default_profile_images/default_profile_4_normal.png" title="jonathonbyrd">'+
							'<p>'+
								'<span class="twttr-reply-screenname">'+ tweet.username +'</span>'+
								data.title +' <a href="'+ api.url + data.path +'" target="_blank" rel="nofollow" class="twitter-timeline-link" data-expanded-url="'+ api.url + data.path +'" title="'+ api.url + data.path +'">'+ api.url + data.path +'</a>'+
							'</p>'+
						'</div>'+
					'</div>' +
					'<div class="twttr-prompt">'+
						'<div class="button twttr-prompt-ok selected">Tweet</div>'+
						'<div class="button twttr-prompt-cancel">Cancel</div>'+
					'</div>'+
				'</div>' 
				).dialog({
					dialogClass: 'retweet-dialog',
					//hide: {effect: "blind", duration: 1000},
					closeOnEscape: true,
					modal: true,
					minWidth: 500,
					title: 'Reply to @swagaways',
					resizable: false,
					open: function()
					{
						
					}
				});
				
				//creating the retweet listener
				api.dialog_ele.find('.twttr-prompt-ok').click(function(){
					var textarea_valu = api.dialog_ele.find('.twitter-anywhere-tweet-box-editor:first').val();
					
					$.ajax({
						method: 'GET',
						data:
						({
							tweet : textarea_valu
						}),
						url: api.tweetit,
						error: function()
						{
							
						},
						success : function(r)
						{
							api.dialog_ele.find('.twttr-dialog-reply-footer:first').html('<p>Thanks!</p>');
							api.dialog_ele.dialog('close');
						}
					});
				});
				
				//cancel button functionality
				api.dialog_ele.find('.twttr-prompt-cancel').click(function(){
					api.dialog_ele.dialog('close');
				});
			});
		},
		
		//Displays a retweet
		startRetweetListener : function()
		{
			$('.retweet-action').click(function(){
				var base64 = $.base64Decode( $(this).parent().attr('data') );
				var data = $.parseJSON( base64 );
				
				//creates the popup inner html
				api.dialog_ele = $( 
				'<div class="twttr-dialog-body clearfix">'+
					'<div class="twttr-dialog-content">'+
						'<div class="retweet twttr-dialog-reply-footer">'+
							'<img src="http://a1.twimg.com/sticky/default_profile_images/default_profile_4_normal.png" title="'+ tweet.username +'">'+
							'<p>'+
								'<span class="twttr-reply-screenname">'+ tweet.username +'</span>'+
								data.title +' <a href="'+ api.url + data.path +'" target="_blank" rel="nofollow" class="twitter-timeline-link" data-expanded-url="'+ api.url + data.path +'" title="'+ api.url + data.path +'">'+ api.url + data.path +'</a>'+
							'</p>'+
						'</div>'+
						'<div class="twttr-prompt">'+
							'<div class="button twttr-prompt-ok selected">Retweet</div>'+
							'<div class="button twttr-prompt-cancel">Cancel</div>'+
						'</div>'+
					'</div>'+
				'</div>' )
				.dialog({
					dialogClass: 'retweet-dialog',
					//hide: {effect: "blind", duration: 1000},
					closeOnEscape: true,
					modal: true,
					minWidth: 500,
					title: 'Retweet this to your followers?',
					resizable: false,
					open: function()
					{
						
					}
				});
				
				//creating the retweet listener
				api.dialog_ele.find('.twttr-prompt-ok').click(function(){
					$.ajax({
						method: 'GET',
						data:
						({
							tweet : data.title +' '+ api.url + data.path
						}),
						url: api.tweetit,
						error: function()
						{
							
						},
						success : function(r)
						{
							api.dialog_ele.find('.twttr-dialog-reply-footer:first').html('<p>Thanks!</p>');
							api.dialog_ele.dialog('close');
						}
					});
				});
				
				//cancel button functionality
				api.dialog_ele.find('.twttr-prompt-cancel').click(function(){
					api.dialog_ele.dialog('close');
				});
			});
		},
		
		addClasses : function()
		{
			$('.tweet-actions').each(function(k,v){
				$(v).parent().addClass('stream-tweet');
			});
		}
		
	};

	//firing the initialization class
	$(document).ready(function($){ tweet.init(); });

})(jQuery);



/**
 * jQuery BASE64 functions
 * 
 * 	<code>
 * 		Encodes the given data with base64. 
 * 		String $.base64Encode ( String str )
 *		<br />
 * 		Decodes a base64 encoded data.
 * 		String $.base64Decode ( String str )
 * 	</code>
 * 
 * Encodes and Decodes the given data in base64.
 * This encoding is designed to make binary data survive transport through transport layers that are not 8-bit clean, such as mail bodies.
 * Base64-encoded data takes about 33% more space than the original data. 
 * This javascript code is used to encode / decode data using base64 (this encoding is designed to make binary data survive transport through transport layers that are not 8-bit clean). Script is fully compatible with UTF-8 encoding. You can use base64 encoded data as simple encryption mechanism.
 * If you plan using UTF-8 encoding in your project don't forget to set the page encoding to UTF-8 (Content-Type meta tag). 
 * This function orginally get from the WebToolkit and rewrite for using as the jQuery plugin.
 * 
 * Example
 * 	Code
 * 		<code>
 * 			$.base64Encode("I'm Persian."); 
 * 		</code>
 * 	Result
 * 		<code>
 * 			"SSdtIFBlcnNpYW4u"
 * 		</code>
 * 	Code
 * 		<code>
 * 			$.base64Decode("SSdtIFBlcnNpYW4u");
 * 		</code>
 * 	Result
 * 		<code>
 * 			"I'm Persian."
 * 		</code>
 * 
 * @alias Muhammad Hussein Fattahizadeh < muhammad [AT] semnanweb [DOT] com >
 * @link http://www.semnanweb.com/jquery-plugin/base64.html
 * @see http://www.webtoolkit.info/
 * @license http://www.gnu.org/licenses/gpl.html [GNU General Public License]
 * @param {jQuery} {base64Encode:function(input))
 * @param {jQuery} {base64Decode:function(input))
 * @return string
 */

(function($){
	
	var keyString = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	
	var uTF8Encode = function(string) {
		string = string.replace(/\x0d\x0a/g, "\x0a");
		var output = "";
		for (var n = 0; n < string.length; n++) {
			var c = string.charCodeAt(n);
			if (c < 128) {
				output += String.fromCharCode(c);
			} else if ((c > 127) && (c < 2048)) {
				output += String.fromCharCode((c >> 6) | 192);
				output += String.fromCharCode((c & 63) | 128);
			} else {
				output += String.fromCharCode((c >> 12) | 224);
				output += String.fromCharCode(((c >> 6) & 63) | 128);
				output += String.fromCharCode((c & 63) | 128);
			}
		}
		return output;
	};
	
	var uTF8Decode = function(input) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
		while ( i < input.length ) {
			c = input.charCodeAt(i);
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			} else if ((c > 191) && (c < 224)) {
				c2 = input.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			} else {
				c2 = input.charCodeAt(i+1);
				c3 = input.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
		}
		return string;
	}
	
	$.extend({
		base64Encode: function(input) {
			var output = "";
			var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
			var i = 0;
			input = uTF8Encode(input);
			while (i < input.length) {
				chr1 = input.charCodeAt(i++);
				chr2 = input.charCodeAt(i++);
				chr3 = input.charCodeAt(i++);
				enc1 = chr1 >> 2;
				enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
				enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
				enc4 = chr3 & 63;
				if (isNaN(chr2)) {
					enc3 = enc4 = 64;
				} else if (isNaN(chr3)) {
					enc4 = 64;
				}
				output = output + keyString.charAt(enc1) + keyString.charAt(enc2) + keyString.charAt(enc3) + keyString.charAt(enc4);
			}
			return output;
		},
		base64Decode: function(input) {
			var output = "";
			var chr1, chr2, chr3;
			var enc1, enc2, enc3, enc4;
			var i = 0;
			input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
			while (i < input.length) {
				enc1 = keyString.indexOf(input.charAt(i++));
				enc2 = keyString.indexOf(input.charAt(i++));
				enc3 = keyString.indexOf(input.charAt(i++));
				enc4 = keyString.indexOf(input.charAt(i++));
				chr1 = (enc1 << 2) | (enc2 >> 4);
				chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
				chr3 = ((enc3 & 3) << 6) | enc4;
				output = output + String.fromCharCode(chr1);
				if (enc3 != 64) {
					output = output + String.fromCharCode(chr2);
				}
				if (enc4 != 64) {
					output = output + String.fromCharCode(chr3);
				}
			}
			output = uTF8Decode(output);
			return output;
		}
	});
})(jQuery);