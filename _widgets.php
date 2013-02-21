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

		if (
			!isset($core->blog->settings->mytwitter->app_token)
			|| !isset($core->blog->settings->mytwitter->app_secret)
			|| !isset($core->blog->settings->mytwitter->user_token)
			|| !isset($core->blog->settings->mytwitter->user_secret)
			|| empty($core->blog->settings->mytwitter->app_token)
			|| empty($core->blog->settings->mytwitter->app_secret)
			|| empty($core->blog->settings->mytwitter->user_token)
			|| empty($core->blog->settings->mytwitter->user_secret)
		) {
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
