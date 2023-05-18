
<?php
require_once 'functionWordpress.php';

function check($data){
if( !isset($data)){
    return 'aucune données';
}

else{
    return $data;
}
}

function in_movie($id,$movies){
    $in = false;
 foreach ($movies as $movie) {
    //echo $movie['id']." / ".$id."<hr>";
    if($id == $movie){
       
        $in = true;
    }
 }
 return $in;

}


function getData($thea,$tok){
    $data ;

    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_URL, "https://graph-api-proxy.allocine.fr/api/query/movieShowtimeList?theater=".$thea."&token=".$tok);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Désactive la vérification SSL
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    /*
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer <token>',
        'Content-Type: application/json'
    ));*/
    
    $response = curl_exec($curl);
    
    if ($response === false) {
        echo 'Erreur cURL : ' . curl_error($curl);
    } else {
        $data = json_decode($response);
        curl_close($curl);
        // Traitez les données de l'API ici
        //var_dump($data->movieShowtimeList->edges[1]);
        $movies = array();
        $allmovies = $data->movieShowtimeList->edges;
        foreach ($allmovies as $onemovie) {
               // var_dump($onemovie);
            if(!empty($onemovie) and !empty($onemovie->node) and !empty($onemovie->node->movie) ){
                $movie = array();
                $movie['id'] = check($onemovie->node->movie->id);
                $movie['link'] = check($onemovie->node->movie->backlink->url);
                $movie['title'] = check($onemovie->node->movie->title);
                $movie['poster'] = check($onemovie->node->movie->poster->url);
                
                if($onemovie->node->movie->releases[0]->releaseDate->date != null){
                    $date = check($onemovie->node->movie->releases[0]->releaseDate->date);
                    // $date = str_replace('-','/',$date);
                    
                    // $date  = DateTime::createFromFormat('d/m/Y', $date);
                    // $newdate =DateTime::date_format($date, 'd-m-Y');
                    $movie['releaseDate'] = $date;
                    
                }
                if(!empty($onemovie->node->movie->credits->edges[0])){
                    $firstName = check($onemovie->node->movie->credits->edges[0]->node->person->firstName);
                    $lastName = check($onemovie->node->movie->credits->edges[0]->node->person->lastName);
                    $movie['director'] = "".$firstName." ".$lastName;
                }else{
                    $firstName ="Aucune";
                    $lastName = "données";
                    $movie['director'] = "".$firstName." ".$lastName;
                }
                $movie['genres'] = check($onemovie->node->movie->genres);
                
                $movie['urlCasting'] =  check($onemovie->node->movie->cast->backlink->url);
                //var_dump($onemovie->node->movie->title);
                //var_dump($onemovie->node->movie->cast->edges[0]->node);
               // var_dump($onemovie->node->movie->title);
                if(!empty($onemovie->node->movie->cast->edges)){
                    if(!empty($onemovie->node->movie->cast->edges[0]->node) ){
                        if(!empty($onemovie->node->movie->cast->edges[0]->node->actor)){
                            $actors ="";
                        foreach ($onemovie->node->movie->cast->edges as  $actor) {
                        $actors = $actors."".$actor->node->actor->firstName." ".$actor->node->actor->lastName." . "; 
                          }
                        $movie['actors'] = $actors;
                        }else{
                            // $firstName ="Aucune";
                            // $lastName = "données";
                            //  $movie['director'] = "".$firstName." ".$lastName;  
                        }
                        
                    }else{
                       // $firstName ="Aucune";
                       // $lastName = "données";
                       //  $movie['director'] = "".$firstName." ".$lastName;
                    }
                   
                }
                else{
    
                }
                
                $movie['synopsis'] = check($onemovie->node->movie->synopsis);
                //var_dump($movie);
                array_push($movies,$movie);    
              
            }
        }
        
        $id_of_movies = getfilms();
      
        if(!empty($id_of_movies)){
            
            foreach($movies as $movie){

                if(in_movie($movie['id'],$id_of_movies)){
                   
                   echo 'no<br>';       
                } else{
                    echo 'add <br>';
                    addFilms($movie); 
                }
            
            }
            
        }
        else{
            foreach ($movies as $movie) {
                addFilms($movie); 
            }
        }
        
         
    }
    
    
    
    //return $data;   
}

