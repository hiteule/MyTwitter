<?php
$core->blog->settings->addNameSpace('mytwitter');

$appToken   = $core->blog->settings->mytwitter->app_token;
$appSecret  = $core->blog->settings->mytwitter->app_secret;
$userToken  = $core->blog->settings->mytwitter->user_token;
$userSecret = $core->blog->settings->mytwitter->user_secret;
$userId     = $core->blog->settings->mytwitter->user_id;

if (isset($_GET['reqAccessToken']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
	$mt = new myTwitter($appToken, $appSecret);
	$req = $mt->requestAssociation(DC_ADMIN_URL . 'plugin.php?p=myTwitter');

	if ($req === false) {
		$core->error->add(__("The update of user access token / secret failed. Please verify your application parameters."));
	}
} elseif (isset($_GET['oauth_token'], $_GET['oauth_verifier'])) {
	$mt = new myTwitter($appToken, $appSecret);
	$finalize = $mt->finalizeAssociation($_GET['oauth_token'], $_GET['oauth_verifier']);

	if ($finalize === true) {
		$userToken  = $core->blog->settings->mytwitter->user_token;
		$userSecret = $core->blog->settings->mytwitter->user_secret;
		$userId     = $core->blog->settings->mytwitter->user_id;

		$successMessage = __('The user access token / secret as been updated.');
	} else {
		$core->error->add(__("The update of user access token / secret failed. Please verify your application parameters."));
	}
}

if (isset($_POST['app_token'], $_POST['app_secret'])) {
	$core->blog->settings->mytwitter->put('app_token', html::escapeHtml($_POST['app_token']), 'string');
	$core->blog->settings->mytwitter->put('app_secret', html::escapeHtml($_POST['app_secret']), 'string');

	$appToken  = html::escapeHtml($_POST['app_token']);
	$appSecret = html::escapeHtml($_POST['app_secret']);

	$successMessage = __('The application parameters as been updated.');
}
?>

<html>
<head>
	<title><?php echo __('MyTwitter'); ?></title>
</head>
<body>
	<h2 class="page-title"><?php echo __('My Twitter'); ?></h2>

	<?php
	if (isset($successMessage) && !empty($successMessage)) {
		echo '<p class="message">' . html::escapeHtml($successMessage) . '</p>';
	}
	?>

	<form method="post" action="<?php echo $p_url; ?>">
	<fieldset>
		<legend><?php echo __('Twitter application parameters'); ?></legend>
		<p>
			<label class="required" for="app_token"><abbr title="<?php echo(__('Required field')); ?>">*</abbr> <?php echo __('Consumer key:'); ?></label>
			<?php echo form::field('app_token', 40, 0, $appToken, 'maximal', 1); ?>

			<label class="required" for="app_secret"><abbr title="<?php echo(__('Required field')); ?>">*</abbr> <?php echo __('Consumer secret:'); ?></label>
			<?php echo form::field('app_secret', 40, 0, $appSecret, 'maximal', 2); ?>
		</p>
		<p>
			<?php echo $core->formNonce(); ?>
			<input type="submit" name="save" value="<?php echo __('Save'); ?>"/>
		</p>
	</fieldset>
	</form>

<fieldset>
	<legend><?php echo __('Twitter user parameters'); ?></legend>

	<label class="required"><?php echo __('Access token:'); ?></label>
	<?php echo $userToken; ?>

	<label class="required"><?php echo __('Access token secret:'); ?></label>
	<?php echo $userSecret; ?>

	<label class="required"><?php echo __('User identifiant:'); ?></label>
	<?php echo $userId; ?>

	<br /><br />
	<form method="post" action="<?php echo $p_url . '&amp;reqAccessToken' ?>">
		<p><input type="submit" value="<?php echo __('Recreate my access token'); ?>"/></p>
	</form>
</fieldset>
</body>
</html>