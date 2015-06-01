<?php

header('Content-Type: application/json');

require 'simplehtmldom_1_5/simple_html_dom.php';

require 'parse-php-sdk/autoload.php';

use Parse\ParseQuery;
use Parse\ParsePush;
use Parse\ParseUser;
use Parse\ParseInstallation;
use Parse\ParseClient;

$weburl = 'http://www.ordemenfermeiros.pt/sites/norte/Paginas/default.aspx';

// Create DOM from URL or file
$html = file_get_html($weburl);

$news = array();

foreach($html->find('table[class=WidthTextoDestaques]') as $elem) {
	$ltd = $elem->find('td[class=LinkTituloDestaques]', 0);
	$tit = $ltd->plaintext;
	$url = $ltd->children[0]->href;
	$date = substr($elem->find('td[class=OEDataDestaques]', 0)->plaintext, 4);

	$post = file_get_html($url);
	$code = $post->find('div[class=OEConteudoTextoArtLeft]',0);

	$txtcode = $code->innertext;

	$img = $code->find('img', 0);
	
	if ($img != null) {
		$src = $img->attr['src'];
		$src = "http://www.ordemenfermeiros.pt" . $src;
		$new = array('title' => $tit, 'url' => $url, 'date' => $date, 'code' => $txtcode, 'img' => $src);
	}
	else {
		$new = array('title' => $tit, 'url' => $url, 'date' => $date, 'code' => $txtcode);
	}

	array_push($news, $new);
}

$posts = json_decode( (file_get_contents( '/var/www/html/srnoe/news.txt' )) , true);

$check = false;
if ($posts[0]['title'] != $news[0]['title'] || 
	$posts[0]['code'] != $news[0]['code']) {
	$check = true;
}

if ($news != null && $check) {
	echo('Novo post!');
	file_put_contents('/var/www/html/srnoe/news.txt', json_encode($news));
	sendNotification($news[0]['title']);
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