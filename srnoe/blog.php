<?php

header('Content-Type: application/json');

require 'simplehtmldom_1_5/simple_html_dom.php';

require 'parse-php-sdk/autoload.php';

use Parse\ParseQuery;
use Parse\ParsePush;
use Parse\ParseUser;
use Parse\ParseInstallation;
use Parse\ParseClient;

//$API_KEY = 'AIzaSyC6UezeWsIqaJibLGfzo9MAKopYn4g_1ng';
//$url = 'https://www.googleapis.com/blogger/v3/blogs/7203463193264811343/posts?key=' . $API_KEY;

$url = 'http://www.bestappsolutions.pt/wordpress/?json=1';

$blogs = file_get_contents($url);

$new = json_decode($blogs, true);

foreach ($new['posts'] as $i=>$blog) {
	$code = str_get_html($blog['content']);
	$img = $code->find('img', 0);
	if ($img != null) {
		$src = $img->attr['src'];
		$new['posts'][$i]['img'] = $src;
	}
}

$posts = json_decode( (file_get_contents( '/var/www/html/srnoe/blogs.txt' )) , true);

$check = false;
if ($posts['posts'][0]['id'] != $new['posts'][0]['id'] || 
	$posts['posts'][0]['title'] != $new['posts'][0]['title'] || 
	$posts['posts'][0]['content'] != $new['posts'][0]['content']) {
	$check = true;
}

if ($new != null && $check) {
	echo('Novo post!');
	file_put_contents('/var/www/html/srnoe/blogs.txt', json_encode($new));
	sendNotification($new['posts'][0]['title']);
}

function sendNotification($title) {

	$app_id = 'vuM4spZvl6kDNqZUb6qTEuf6Y7OVUZ23xoBjzEOh';
	$rest_key = 'vsW3shgjBGlt9Diqu4Rdqjr2F1sUbGhrvbwREkMU';
	$master_key = 'yonGCoIvsRgaU100Ei3KjRigIvWlGGWweh4Ogthz';

	ParseClient::initialize( $app_id, $rest_key, $master_key );

	$notification = $title;

	$query = ParseInstallation::query();
	$query->equalTo("deviceType", "android");

	$data = array("alert" => $notification/*, "title" => "qwerty"*/);

	$aux = ParsePush::send(array(
  		"where" => $query,
  		"data" => $data,
	));
}

?>