<?php
if (!defined('DC_CONTEXT_ADMIN')) {
	return;
}

require dirname(__FILE__) . '/_widgets.php';

$_menu['Plugins']->addItem(
	__('My Twitter'),
	'plugin.php?p=myTwitter',
	'index.php?pf=myTwitter/icon-small.png',
	preg_match('/plugin.php\?p=myTwitter(&.*)?$/', $_SERVER['REQUEST_URI']),
	$core->auth->check('usage,contentadmin', $core->blog->id)
);

$core->addBehavior('adminDashboardFavs', array('myTwitterBehaviors', 'dashboardFavs'));

class myTwitterBehaviors
{
	public static function dashboardFavs($core, $favs)
	{
		$favs['myTwitter'] = new ArrayObject(array(
			'myTwitter',
			__('My Twitter'),
			'plugin.php?p=myTwitter',
			'index.php?pf=myTwitter/icon-small.png',
			'index.php?pf=myTwitter/icon-big.png',
			'usage,contentadmin',
			null,
			null
		));
	}
}
?>