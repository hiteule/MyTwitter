<?php
class myTwitter extends tmhOAuth
{
    const CACHE_LIFETIME = 120;

    const REQUEST_TOKEN_URL = 'https://api.twitter.com/oauth/request_token';
	const AUTHORIZE_URL     = 'https://api.twitter.com/oauth/authorize';
	const ACCESS_TOKEN_URL  = 'https://api.twitter.com/oauth/access_token';
    const STATUES_USER_URL  = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

    private $_cacheFileName = null;

	public function __construct($appToken, $appSecret, $userToken = null, $userSecret = null)
	{
        $this->_cacheFileName = dirname(__FILE__) . '/cache.json';

		parent::__construct(array(
			'consumer_key'    => $appToken,
			'consumer_secret' => $appSecret,
			'timezone'        => date_default_timezone_get(),
		));
	}

	public function requestAssociation($callback)
	{
		$code = $this->request(
			'POST',
			self::REQUEST_TOKEN_URL,
			array(
				'oauth_callback' => $callback,
			)
		);

		if ($code === 200) {
			$tokens = $this->extract_params($this->response['response']);

            if (isset($tokens['oauth_token'], $tokens['oauth_token_secret']) && !empty($tokens['oauth_token']) && !empty($tokens['oauth_token_secret'])) {
                $_SESSION['myTwitter']['oauth_token_secret'] = $tokens['oauth_token_secret'];

                $authorizeUrl = self::AUTHORIZE_URL . '?oauth_token=' . $tokens['oauth_token'] . '&hd=default';
                header('Location:' . $authorizeUrl);
            }
		}

        return false;
	}

	public function finalizeAssociation($token, $verifier)
    {
    	global $core;
    	
        if (isset($_SESSION['myTwitter'], $_SESSION['myTwitter']['oauth_token_secret'])) {
            $this->config['user_token']  = $token;
            $this->config['user_secret'] = $_SESSION['myTwitter']['oauth_token_secret'];

            $code = $this->request(
                'POST',
                self::ACCESS_TOKEN_URL,
                array(
                    'oauth_verifier' => $verifier,
                )
            );

            unset($_SESSION['myTwitter']);

            if ($code == 200) {
                $tokens = $this->extract_params($this->response['response']);
            
                if (!empty($tokens['oauth_token']) && !empty($tokens['oauth_token_secret']) && !empty($tokens['user_id'])) {
                    $core->blog->settings->mytwitter->put('user_token', $tokens['oauth_token'], 'string');
					$core->blog->settings->mytwitter->put('user_secret', $tokens['oauth_token_secret'], 'string');
					$core->blog->settings->mytwitter->put('user_id', $tokens['user_id'], 'string');

                    return true;
                }
            }
        }
        return false;
    }

    private function getAssociation()
    {
        global $core;

        $userToken  = $core->blog->settings->mytwitter->user_token;
        $userSecret = $core->blog->settings->mytwitter->user_secret;
        $userId     = $core->blog->settings->mytwitter->user_id;

        return array(
            'userToken'  => $userToken,
            'userSecret' => $userSecret,
            'userId'     => $userId,
        );
    }

    public function isAssociated()
    {
        $assoc = $this->getAssociation();

        if (empty($assoc['userToken']) || empty($assoc['userSecret']) || empty($assoc['userId'])) {
            return false;
        }

        return true;
    }

    public function getLastTweet($limit = 200)
    {
        $cache = $this->_getCache();

        if (isset($cache['expire'], $cache['tweets']) && $cache['expire'] > time(null)) {
            return $this->_sliceTweetList($cache['tweets'], $limit);
        }

        if (!$this->isAssociated()) {
            return false;
        }

        $assoc = $this->getAssociation();

        $this->config['user_token']  = $assoc['userToken'];
        $this->config['user_secret'] = $assoc['userSecret'];

        $code = $this->request(
            'GET',
            self::STATUES_USER_URL,
            array(
                'user_id' => $assoc['userId'],
                'count'   => 200,
            )
        );

        if ($code == 200) {
            $tweets = $this->_formatTweetList($this->response['response']);
            $this->_setCache($tweets);

            return $this->_sliceTweetList($tweets, $limit);
        }

        return false;
    }

    private function _formatTweetList($response)
    {
        $response = json_decode($response, true);

        $tweets = array();

        if (is_array($response)) {
            foreach ($response as $k => $v) {
                $tweets[] = array(
                    'id'            => $v['id'],
                    'text'          => $v['text'],
                    'created_at'    => $v['created_at'],
                    'username'      => $v['user']['screen_name'],
                    'name'          => $v['user']['name'],
                    'hashtags'      => $v['entities']['hashtags'],
                    'urls'          => $v['entities']['urls'],
                    'user_mentions' => $v['entities']['user_mentions'],
                );
            }
        }

        return $tweets;
    }

    private function _sliceTweetList($tweets, $limit)
    {
        return array_slice($tweets, 0, (int)$limit);
    }

    private function _setCache($tweets)
    {
        $cache = array(
            'expire' => time(null) + self::CACHE_LIFETIME,
            'tweets' => $tweets,
        );
        $cache = json_encode($cache);

        file_put_contents($this->_cacheFileName, $cache);
    }

    private function _getCache()
    {
        if (!file_exists($this->_cacheFileName)) {
            return false;
        }

        $cache = file_get_contents($this->_cacheFileName);

        if ($cache !== false) {
            $cache = json_decode($cache, true);
        }

        return $cache;
    }

    public static function formatTweet($text, $hashtags = array(), $urls = array(), $user_mentions = array())
    {
        $text = htmlentities($text);

        foreach ($hashtags as $kHt => $vHt) {
            $text = str_replace('#' . htmlentities($vHt['text']), '<a href="http://twitter.com/search?q=' . urlencode('#' . htmlentities($vHt['text'])) . '&amp;src=hash" target="_blank">#' . htmlentities($vHt['text']) . '</a>', $text);
        }
        foreach ($urls as $kUrl => $vUrl) {
            $text = str_replace($vUrl['url'], '<a href="' . $vUrl['url'] . '" target="_blank">' . $vUrl['display_url'] . '</a>', $text);
        }
        foreach ($user_mentions as $kUm => $vUm) {
            $text = str_replace('@' . htmlentities($vUm['screen_name']), '<a href="http://twitter.com/' . htmlentities($vUm['screen_name']) . '" target="_blank">@' . htmlentities($vUm['screen_name']) . '</a>', $text);
        }

        return $text;
    }

    public static function formatDate($time)
    {
        $timeTweet = strtotime($time);
        $timeDiff  = time(null) - $timeTweet;

        if ($timeDiff < 60) { // sec
            return $timeDiff . ' ' . __('s');
        } elseif ($timeDiff < 3600) { // min
            return round(($timeDiff / 60), 0, PHP_ROUND_HALF_UP) . ' ' . __('min');
        } elseif ($timeDiff < 86400) { // h
            return ceil(($timeDiff / 86400)) . ' ' . __('h');
        }

        return dt::str("%e %b", strtotime($time));
    }
}
?>
