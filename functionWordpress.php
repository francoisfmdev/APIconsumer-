<?php

function getToken($username, $password, $url) {
    $endpoint = $url . '/wp-json/api/v1/token';
    $ch = curl_init($endpoint);

    $data = array(
        'username' => $username,
        'password' => $password
    );
    $payload = json_encode($data);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

    $result = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($result, true);
    var_dump($result);
    // Vérifiez si la clé 'token' existe dans la réponse (remplacez 'token' par la clé appropriée pour miniOrange)
    if (isset($response['jwt_token'])) {
       return $response['jwt_token']; 
    } else {
        // Gérez l'erreur en fonction de vos besoins, par exemple en renvoyant null ou en affichant un message d'erreur
        return null;
    }

}

function addCustomPostType($url, $token, $custom_post_type, $title, $content, $acf_fields) {
  $endpoint = $url . '/wp-json/wp/v2/' . $custom_post_type;
  $ch = curl_init($endpoint);

  $data = array(
      'title' => $title,
      'content' => $content,
      'status' => 'publish',
      'acf' => $acf_fields,
     
  );
  $payload = json_encode($data);
 //var_dump($endpoint);
 //var_dump($payload);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type:application/json',
      'Authorization: Bearer ' . $token // Assurez-vous d'utiliser le mot-clé "Bearer" suivi d'un espace et du token
  ));
  
  $result = curl_exec($ch);
  curl_close($ch);
  var_dump($result);
 


}




function getfilms(){

    $url = 'https://www.cine220.fmdevschool.fr';
    $username = 'fmdev@2022';
    $password = '@Lucifor1988@';
    $custom_post_type = 'film';

    $token = getToken($username,$password,$url);

   // var_dump($token);
    
    $endpoint = $url . '/wp-json/wp/v2/film?per_page=100';
    $ch = curl_init($endpoint);curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type:application/json',
        'Authorization: Bearer ' . $token // Assurez-vous d'utiliser le mot-clé "Bearer" suivi d'un espace et du token
    ));

    $result = curl_exec($ch);
  curl_close($ch);
  //var_dump($result);
  $movies = json_decode($result);
 
//var_dump($films);
    $title_of_movies = array();
   foreach ($movies as $film) {
      //var_dump($film->title);
      array_push($title_of_movies,$film->acf->id);
   }
   return $title_of_movies;
}





function addFilms($movie){
  $url = 'https://www.cine220.fmdevschool.fr';
  $username = 'fmdev@2022';
  $password = '@Lucifor1988@';
  $custom_post_type = 'film';
  $title = $movie['title'];
  $content = $movie['synopsis'];
  $genre1;
  $genre2;
  $genre3;
  $acf_fields;

   if($movie['genres'][0] and $movie['genres'][1] AND $movie['genres'][2]){
     $genre1 = strtolower($movie['genres'][0]);
     $genre2 =  strtolower($movie['genres'][1]);
     $genre3 = strtolower( $movie['genres'][2]);
     $acf_fields = array(
      'id' => $movie['id'],
      'link' => $movie['link'],
      'title'=> $movie['title'],
      'poster'=> $movie['poster'],
      'releasedate'=> $movie['releaseDate'],
      'director' => $movie['director'],
      'urlcasting'=> $movie['urlCasting'],
      'actors'=> $movie['actors'],
      'synopsis' => $movie['synopsis'],
      'genre_1' => $genre1,
      'genre_2' => $genre2,
      'genre_3' => $genre3,
    );
   }
   elseif($movie['genres'][0] and $movie['genres'][1] ){
    $genre1 = strtolower($movie['genres'][0]);
    $genre2 = strtolower($movie['genres'][1]);
    $acf_fields = array(
      'id' => $movie['id'],
      'link' => $movie['link'],
      'title'=> $movie['title'],
      'poster'=> $movie['poster'],
      'releasedate'=> $movie['releaseDate'],
      'director' => $movie['director'],
      'urlcasting'=> $movie['urlCasting'],
      'actors'=> $movie['actors'],
      'synopsis' => $movie['synopsis'],
      'genre_1' => $genre1,
      'genre_2' => $genre2,
    
    );
   }elseif($movie['genres'][0] ){
    $genre1 = strtolower($movie['genres'][0]);
    $acf_fields = array(
      'id' => $movie['id'],
      'link' => $movie['link'],
      'title'=> $movie['title'],
      'poster'=> $movie['poster'],
      'releasedate'=> $movie['releaseDate'],
      'director' => $movie['director'],
      'urlcasting'=> $movie['urlCasting'],
      'actors'=> $movie['actors'],
      'synopsis' => $movie['synopsis'],
      'genre_1' => $genre1,
     
    );
   }

    
    $token = getToken($username, $password, $url);
 
    $response = addCustomPostType($url, $token, $custom_post_type, $title, $content, $acf_fields);
    
    
    var_dump($response);
  


}
?>