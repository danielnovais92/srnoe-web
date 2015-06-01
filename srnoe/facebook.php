<?php

header('Content-Type: application/json');

require_once( 'Facebook/HttpClients/FacebookHttpable.php' );
require_once( 'Facebook/HttpClients/FacebookCurl.php' );
require_once( 'Facebook/HttpClients/FacebookCurlHttpClient.php' );
require_once( 'Facebook/Entities/AccessToken.php' );
require_once( 'Facebook/Entities/SignedRequest.php' );
require_once( 'Facebook/FacebookSession.php' );
require_once( 'Facebook/FacebookRedirectLoginHelper.php' );
require_once( 'Facebook/FacebookRequest.php' );
require_once( 'Facebook/FacebookResponse.php' );
require_once( 'Facebook/FacebookSDKException.php' );
require_once( 'Facebook/FacebookRequestException.php' );
require_once( 'Facebook/FacebookOtherException.php' );
require_once( 'Facebook/FacebookAuthorizationException.php' );
require_once( 'Facebook/GraphObject.php' );
require_once( 'Facebook/GraphSessionInfo.php' );
 
use Facebook\HttpClients\FacebookHttpable;
use Facebook\HttpClients\FacebookCurl;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\Entities\AccessToken;
use Facebook\Entities\SignedRequest;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookOtherException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphSessionInfo;

require 'parse-php-sdk/autoload.php';

use Parse\ParseQuery;
use Parse\ParsePush;
use Parse\ParseUser;
use Parse\ParseInstallation;
use Parse\ParseClient;

FacebookSession::setDefaultApplication('858851767462997', 'e3c368a8edb4e68b41a21582d2ec7d04');

$session = new FacebookSession('858851767462997|7jI0SwqrslcJnKthM6r0MAVVsJ0');

// To validate the session:
try {
  $session->validate();
} catch (FacebookRequestException $ex) {
  // Session not valid, Graph API returned an exception with the reason.
  echo $ex->getMessage();
} catch (\Exception $ex) {
  // Graph API returned info, but it may mismatch the current app or have expired.
  echo $ex->getMessage();
}

/* make the API call */
$request = new FacebookRequest(
  $session,
  'GET',
  '/497566220296597/feed?fields=id,message,description,type,created_time,picture,link&limit=15'
);

$response = $request->execute();
$new = $response->getGraphObject();

/* handle the result */
$new = json_decode(json_encode($new->asArray()),true);

$posts = json_decode((file_get_contents( '/var/www/html/srnoe/fbposts.txt' )) , true);

$check = false;
if ($posts['data'][0]['id'] != $new['data'][0]['id'] || 
	$posts['data'][0]['message'] != $new['data'][0]['message']) {
	$check = true;
}

if ($new != null && $check) {
	echo('Novo post!');
	file_put_contents('/var/www/html/srnoe/fbposts.txt', json_encode($new));
	sendNotification($new['data'][0]['id']);
	file_put_contents('/var/www/html/srnoe/fblog.txt', json_encode($posts));
}

//echo json_encode($graphObject, JSON_PRETTY_PRINT);

function sendNotification($id) {

	$app_id = 'vuM4spZvl6kDNqZUb6qTEuf6Y7OVUZ23xoBjzEOh';
	$rest_key = 'vsW3shgjBGlt9Diqu4Rdqjr2F1sUbGhrvbwREkMU';
	$master_key = 'yonGCoIvsRgaU100Ei3KjRigIvWlGGWweh4Ogthz';

	ParseClient::initialize( $app_id, $rest_key, $master_key );

	$notification = utf8_encode("A SRN da Ordem dos Enfermeiros publicou no Facebook.");

	$query = ParseInstallation::query();
	$query->equalTo("deviceType", "android");

	$data = array("alert" => $notification, "id" => $id);

	$aux = ParsePush::send(array(
  		"where" => $query,
  		"data" => $data
	));
}

?>