<?php 
if (!defined('DC_RC_PATH')) {
	return;
}

require dirname(__FILE__) . '/_widgets.php';

class myTwitterDocument extends dcUrlHandlers
{
	public static function page($args)
	{
		global $core;

		$appToken  = $core->blog->settings->mytwitter->app_token;
		$appSecret = $core->blog->settings->mytwitter->app_secret;
		$mt = new myTwitter($appToken, $appSecret);

		$_ctx =& $GLOBALS['_ctx'];

		$tweets = $mt->getLastTweet();
		if ($tweets === false) {
			$tweets = array();
		}

		$_ctx->tweets = $tweets;

		$core->tpl->setPath($core->tpl->getPath(), dirname(__FILE__) . '/tpl/');
 
		self::serveDocument('mytwitter.html', 'text/html');
	}
}

$core->tpl->addBlock('tweets', array('myTwitterTpl', 'tweets'));

class myTwitterTpl
{
	public static function tweets($attr, $content)
	{
		return ('<?php
		foreach ($_ctx->tweets as $k => $v) {
			$tweetUrl   = "http://twitter.com/" . $v["username"] . "/status/" . $v["id"];
			$date       = myTwitter::formatDate($v["created_at"]);
			$profilUrl  = "http://twitter.com/" . htmlentities($v["username"]);
			$name       = htmlentities($v["name"]);
			$screenname = htmlentities($v["username"]);
			$text       = myTwitter::formatTweet($v["text"], $v["hashtags"], $v["urls"], $v["user_mentions"]);
			echo sprintf("' . $content . '", $tweetUrl, $date, $profilUrl, $name, $screenname, $text);
		}
		?>');
	}
}

class publicMyTwitterWidget
{
	public static function myTwitterWidget($w)
	{
		global $core;

		if ($w->homeonly && $core->url->type != 'default') {
			return;
		}

		$appToken  = $core->blog->settings->mytwitter->app_token;
		$appSecret = $core->blog->settings->mytwitter->app_secret;
		$mt = new myTwitter($appToken, $appSecret);

		$tweets = $mt->getLastTweet($w->limit);
		if ($tweets === false) {
			$tweets = array();
		}

		$html = '<div class="myTwitter">';
		$html .= '<h2>' . htmlentities($w->title) . '</h2>';
		$html .= '<ul>';
		foreach ($tweets as $k => $v) {
			$date = myTwitter::formatDate($v['created_at']);

			$text = myTwitter::formatTweet($v['text'], $v['hashtags'], $v['urls'], $v['user_mentions']);

			$html .= '<li>';
			$html .= '<div class="date"><a href="http://twitter.com/' . htmlentities($v['username']) . '/status/' . (int)$v['id'] . '" target="_blank">' . $date . '</a></div>';
			$html .= '<div class="author"><a href="http://twitter.com/' . htmlentities($v['username']) . '" target="_blank" class="name">' . htmlentities($v['name']) . '</a> <span class="screenname"><span class="at">@</span>' . htmlentities($v['username']) . '</span></div>';
			$html .= $text;
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= '</div>';

		return $html;
	}
}

$core->addBehavior('publicHeadContent', array('myTwitterStyle', 'publicHeadContent'));

class myTwitterStyle
{
	public static function publicHeadContent($core)
	{
		$style = file_get_contents(dirname(__FILE__) . '/style.css');

		echo '<style type="text/css" media="screen">' . "\n" . $style . "</style>\n";
	}
}
?>
