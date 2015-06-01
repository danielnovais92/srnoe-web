<?php
$dados=$_POST['noise'];

date_default_timezone_set("Europe/Lisbon");
$date=date("h:i:sa");
$response = array();


if (isset($_POST['noise']))
{
  $response["success"] = 1;
  $response["message"] = $dados;
  $response["date"] = $date;
    //echo $dados;
    //print_r($dados);
   // echo strip_tags_content($dados, '<b>');
   // echo json_encode($response);
    //$results = print_r($response, true);
   //echo "ola";
  $data = file_get_contents( '/var/www/html/nurses/teste.txt' );
  $resultsOld = print_r($data, true);
    //print_r($resultsOld); 
  
  //  file_put_contents('teste.txt', print_r($response, true));
  $results = print_r($response, true);
  $tot=$results.$resultsOld;
  file_put_contents('/var/www/html/nurses/teste.txt', json_encode($tot, true));
   //file_put_contents('/tmp/test.txt', 'this is my content' );
    //escreve($tot);
  echo "Publicaçao efetuada com sucesso!!";

}
else
{
  $response["success"] = 0;
  echo "Nao digitou nada";
}

?>