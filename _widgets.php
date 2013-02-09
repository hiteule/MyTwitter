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
