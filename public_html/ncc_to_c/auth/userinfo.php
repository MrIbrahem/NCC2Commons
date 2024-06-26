<?php

if (isset($_REQUEST['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
header('Content-type: application/json; charset=utf-8');
// Require the library and set up the classes we're going to use in this second part of the demo.
require_once __DIR__ . '/../vendor/autoload.php';

use MediaWiki\OAuthClient\Client;
use MediaWiki\OAuthClient\ClientConfig;
use MediaWiki\OAuthClient\Consumer;
use MediaWiki\OAuthClient\Token;

// Get the wiki URL and OAuth consumer details from the config file.
require_once __DIR__ . '/config.php';

// Configure the OAuth client with the URL and consumer details.
$conf = new ClientConfig($oauthUrl);
$conf->setConsumer(new Consumer($consumerKey, $consumerSecret));
$conf->setUserAgent($gUserAgent);
$client = new Client($conf);

// Get the Request Token's details from the session and create a new Token object.
session_start();
// Load the Access Token from the session.
$accessToken = new Token(
	$_SESSION['access_key'],
	$_SESSION['access_secret']
);

// Example 1: get the authenticated user's identity.
$ident = $client->identify($accessToken);
// Use htmlspecialchars to properly encode the output and prevent XSS vulnerabilities.
// echo "You are authenticated as " . htmlspecialchars($ident->username) . ".\n\n";
//---
$_SESSION['username'] = $ident->username;
//---
// Example 2: do a simple API call.
$userInfo = json_decode($client->makeOAuthCall(
	$accessToken,
	"$apiUrl?action=query&meta=userinfo&uiprop=rights&format=json"
));
// echo "== User info ==<br><br>";

echo json_encode($userInfo, JSON_PRETTY_PRINT);

function get_user_name()
{
	global $ident;
	return $ident->username;
}
// Example 3: make an edit (getting the edit token first).
# automatic redirect to edit.php
// header( 'Location: edit.php' );
