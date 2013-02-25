<?php
if (!defined('DC_CONTEXT_ADMIN')) {
	return;
}

$mVersion = $core->plugins->moduleInfo('myTwitter', 'version');
$iVersion = $core->getVersion('myTwitter');

if (version_compare($iVersion, $mVersion, '>=')) {
	return;
}

$core->blog->settings->addNamespace('mytwitter');

$core->blog->settings->mytwitter->put('app_token', '', 'string', null, false);
$core->blog->settings->mytwitter->put('app_secret', '', 'string', null, false);
$core->blog->settings->mytwitter->put('user_token', '', 'string', null, false);
$core->blog->settings->mytwitter->put('user_secret', '', 'string', null, false);
$core->blog->settings->mytwitter->put('user_id', '', 'string', null, false);

$core->setVersion('myTwitter', $mVersion);
