<?php
if (!defined('DC_RC_PATH')) {
	return;
}

$core->url->register('myTwitter', 'myTwitter', '^myTwitter(?:/(.+))?$', array('myTwitterDocument', 'page'));

$__autoload['tmhOAuth'] = dirname(__FILE__) . '/tmhOAuth.php';
$__autoload['myTwitter'] = dirname(__FILE__) . '/class.myTwitter.php';
?>