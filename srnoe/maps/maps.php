<?php



$title=$_POST['title'];
$data=$_POST['date'];
$lat=$_POST['lat'];
$long=$_POST['long'];
$desc=$_POST['desc'];


if (isset($_POST['title']) && isset($_POST['date']) && isset($_POST['lat']) && isset($_POST['long']) && isset($_POST['desc'])) {

  	//echo $title."++".$data."++".$lat."++".$long."++".$desc;
  	$new = array('title' => $title, 'date' => $data, 'lat' => $lat , 'long' => $long , 'desc' => $desc);
    //echo json_encode($new);
  $posts = json_decode( (file_get_contents( '/var/www/html/srn-oe/maps/maps.txt' )) , true);

  array_unshift($posts, $new);
  
  file_put_contents('/var/www/html/srn-oe/maps/maps.txt', json_encode($posts));

  echo "Pin-Point adicionado com sucesso!!";

	

}
else {
  echo "Nao digitou nada";
}




?>