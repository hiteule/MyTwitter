<?php
if (!defined('DC_RC_PATH')) {
	return;
}

$core->addBehavior(
	'initWidgets',
	array('myTwitterWidgetBehaviors', 'initMyTwitterWidgets')
);
 
class myTwitterWidgetBehaviors
{
	public static function initMyTwitterWidgets($w)
	{
		global $core;

		if (!is_object($core->blog->settings->mytwitter)) {
			return;
		}

		$appToken   = $core->blog->settings->mytwitter->app_token;
		$appSecret  = $core->blog->settings->mytwitter->app_secret;
		$userToken  = $core->blog->settings->mytwitter->user_token;
		$userSecret = $core->blog->settings->mytwitter->user_secret;

		if (empty($appToken) || empty($appSecret) || empty($userToken) || empty($userSecret)) {
			return;
		}

		$w->create(
			'MyTwitterWidget',
			'My Twitter',
			array('publicMyTwitterWidget','myTwitterWidget')
		);

		$w->MyTwitterWidget->setting(
			'title',
			__('Title:'),
			'My Twitter',
			'text'
		);

		$w->MyTwitterWidget->setting(
			'limit',
			__('Number of tweet to display:'),
			'10',
			'text'
		);

		$w->MyTwitterWidget->setting(
			'homeonly',
			__('Home page only'),
			false,
			'check'
		);
	}
}
