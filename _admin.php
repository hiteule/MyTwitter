<?php
if (!defined('DC_CONTEXT_ADMIN')) {
	return;
}

require dirname(__FILE__) . '/_widgets.php';

# ajouter le plugin dans la liste des plugins du menu de l'administration
$_menu['Plugins']->addItem(
	__('My Twitter'),
	'plugin.php?p=myTwitter',
	'index.php?pf=myTwitter/icon-small.png',
	preg_match('/plugin.php\?p=myTwitter(&.*)?$/', $_SERVER['REQUEST_URI']),
	$core->auth->check('usage,contentadmin', $core->blog->id)
);
?>