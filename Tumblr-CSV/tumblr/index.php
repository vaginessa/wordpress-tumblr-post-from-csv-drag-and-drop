<?php
/**
 * @file
 * User has successfully authenticated with Tumblr. Access tokens saved to session and DB.
 */

/* Load required lib files. */
require_once('tumblroauth/tumblroauth.php');
require_once('config.php');

/* If access tokens are not available redirect to connect page. */
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    
}
/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];

/* Create a TumblrOauth object with consumer/user tokens. */
$connection = new TumblrOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

/* Find User Info */
$user_info = $connection->post('user/info');

$content = $user_info;

/* Some example calls */
/* Find Primary Blog Hostname */
/*
foreach($user_info->response->user->blogs as $blog){
	if($blog->primary === true){
		break;
	}
}
$hostname = parse_url($blog->url,PHP_URL_HOST);
*/
// $connection->get("blog/$hostname/info"); /* Not Yet Working */
// $connection->post("blog/$hostname/post", array('type' => 'text', 'body' => 'Testing TumblrOAuth - ' . date(DATE_RFC822)));
// $connection->post("user/follow", array('url' => 'http://nquinlan.tumblr.com/'));
// $connection->post("user/unfollow", array('url' => 'http://nquinlan.tumblr.com/'));

/* Include HTML to display on the page */
