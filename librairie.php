<?php

 include('jwt_utils.php');

function getConnection() {

    $server="localhost";
    $login="root";
    $mdp='';
    $db ="db_articlesblogs";

    $linkpdo = '';

    try {
        $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
    }
    catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }

    return $linkpdo ;

}



#Permet de voir tout les articles (Auteur , contenu , date de Publication) pour une personne non authentifié 
function actionGet($linkpdo) {
    $query = $linkpdo->query('SELECT  idArticle ,  nomAuteur as "Auteur" , contenu  , datePublication as "Date de Publication" FROM article INNER JOIN utilisateur on article.idUtilisateur = utilisateur.idUtilisateur ORDER BY 3');

    if ($query == false) {
        die('Erreur query dans la fonction actionGet');
    }

    while($donnees2 = $query->fetch(PDO::FETCH_ASSOC)) {
        $articles[] = $donnees2;
    }
    return $articles ;

}

function actionPost($contenu, $idUtilisateur,  $linkpdo) {
    $query = $linkpdo->prepare('INSERT INTO article(datePublication, contenu, idUtilisateur) VALUES (:datePublication, :contenu, :idUtilisateur)');
    
    if ($query == false) {
        die('Erreur prepare dans la fonction actionPost');
    }

    $query->bindValue(':datePublication', date("Y-m-d H:i:s"));
    $query->bindValue(':contenu', $contenu );
    $query->bindValue(':idUtilisateur', $idUtilisateur);
    $query->execute();
}

function actionPutById($id, $contenu,  $linkpdo) {
    $query = $linkpdo->prepare('UPDATE article SET contenu = :contenu WHERE idArticle = :idArticle');

    if ($query == false) {
        die('Erreur prepare dans la fonction actionPutById');
    }

    $query->bindValue(':contenu', $contenu);
    $query->bindValue(':idArticle', $id);
    $query->execute();

}


function actionDeleteById($id,$linkpdo) {
    $query = $linkpdo->query('DELETE FROM aimer WHERE idArticle ='. $id);
    $query2 = $linkpdo->query('DELETE FROM article WHERE idArticle ='. $id);

    if ($query == false || $query2 == false) {
        die('Erreur prepare dans la fonction actionDeleteById');
    } 

    return($query2->rowCount() > 0 ) ;
    
}


//test recuperation de la fonction
function isUserAuthor($idArticle, $user, $linkpdo) {
    
    $query = $linkpdo->prepare('SELECT u.userlogin FROM utilisateur u JOIN article a on u.idUtilisateur = a.idUtilisateur WHERE a.idArticle = ?');
    $query->execute([$idArticle]);
    $author = $query->fetch();

    if ($query == false) {
        die('Erreur prepare dans la fonction isUserAuthor');
    }
    if ($author == false) {
        die('Article not found');
    }else {
        return ($author[0] == $user) ;
    }
    



}

function isValidUser($userlogin, $userpassword,  $linkpdo) {
 
    $requete = $linkpdo->prepare('SELECT motDePasse FROM utilisateur WHERE userlogin = ?');
    $requete->execute([$userlogin]);
    $password = $requete->fetch(); 
    
    if ($requete == false) {
        die('Erreur query dans la fonction isValidUser');
    }
    return (password_verify($userpassword, $password[0]));
}





function actionPostAuth($userlogin, $userpassword, $linkpdo) {

    if (isValidUser($userlogin, $userpassword, $linkpdo)) {

        $requete = $linkpdo->prepare('SELECT userrole FROM utilisateur WHERE userlogin = ?');
        $requete->execute([$userlogin]);
        $role = $requete->fetch(); 
        echo $role ;

        $headers = array('alg' => 'HS256', 'typ' => 'JWT');
        $payload = array('username' => $userlogin, 'role' => $role, 'exp' =>(time() + 60));
        //Création du token
        $token = generate_jwt($headers, $payload);

        return $token ;

    } else {
        return FALSE;
    }



}



function getLikePublisher($linkpdo , $article){
    foreach ($article as $art){
        $query = $linkpdo->query('SELECT idArticle,TypeLike , COUNT(TypeLike) as "Nombre" from aimer where idArticle='. $art['idArticle'].' GROUP By(`TypeLike`);');
        if ($query == false) {
            die('Erreur query dans la fonction getLikePublisher');
        }
        while($donnees3 = $query->fetch(PDO::FETCH_ASSOC)) {
            if($donnees3['TypeLike'] == 0){
                $donnees3['TypeLike'] = 'dislike';
            } else {
                $donnees3['TypeLike'] = 'like';
            }
            $likes[] = $donnees3;
        }
    }
    for ($i = 0 ,$size = count($article); $i < $size ; $i++) {
        for ($j = 0 ,$sizeLike = count($likes); $j < $sizeLike ; $j++) {
            if($article[$i]['idArticle']==$likes[$j]['idArticle'])
                array_push($article[$i],$likes[$j]);
       }
    }
    return $article ;
}


function getLikeModerateur($linkpdo , $article){
    foreach ($article as $art){
        $query = $linkpdo->query('SELECT nomAuteur , idArticle, CASE When TypeLike = "0" Then "dislike"else "like"END as TypeLike from aimer join utilisateur on utilisateur.idUtilisateur = aimer.idUtilisateur where idArticle='. $art['idArticle'].' GROUP By(aimer.idUtilisateur);');
        if ($query == false) {
            die('Erreur query dans la fonction getLikeModerateur');
        }
        while($donnees4 = $query->fetch(PDO::FETCH_ASSOC)) {
            $likes[] = $donnees4;
        }
    }
    for ($i = 0 ,$size = count($article); $i < $size ; $i++) {
        for ($j = 0 ,$sizeLike = count($likes); $j < $sizeLike ; $j++) {
            if($article[$i]['idArticle']==$likes[$j]['idArticle']){
                if($article[$i][0]['TypeLike']==$likes[$j]['TypeLike']){
                    array_push($article[$i][0],$likes[$j]['nomAuteur']);
                }elseif($article[$i][1]['TypeLike']==$likes[$j]['TypeLike']){
                    array_push($article[$i][1],$likes[$j]['nomAuteur']);
                }
            }
        }
        unset($article[$i][0]['idArticle']);
        unset($article[$i][1]['idArticle']);
    }
    return $article ;
}

function actionGetMesArticles($linkpdo,$user) {
    $query = $linkpdo->query("SELECT  idArticle ,  nomAuteur as 'Auteur' , contenu  , datePublication as 'Date de Publication' FROM article JOIN utilisateur on article.idUtilisateur = utilisateur.idUtilisateur WHERE utilisateur.userlogin = '$user' ORDER BY 3;");

    if ($query == false) {
        die('Erreur query dans la fonction actionGet');
    }

    while($donnees5 = $query->fetch(PDO::FETCH_ASSOC)) {
        $articles[] = $donnees5;
    }
    return $articles ;

}

function actionPostLikeArticle($like, $idUtilisateur,$idArticle,$linkpdo) {
    $query = $linkpdo->prepare('INSERT INTO aimer (idArticle, idUtilisateur, TypeLike) VALUES (:idArticle, :idUtilisateur, :Typelike)');
    
    if ($query == false) {
        die('Erreur prepare dans la fonction actionPostArticle');
    }

    $query->bindValue(':idArticle', $idArticle );
    $query->bindValue(':idUtilisateur', $idUtilisateur);
    $query->bindValue(':Typelike', $like);
    
    $query->execute();
}



function getIdByUser($username, $linkpdo) {
    $query = $linkpdo->prepare('SELECT u.idUtilisateur FROM utilisateur u  WHERE u.userlogin = ?');
    $query->execute([$username]);
    $idUser = $query->fetch();

    if ($query == false) {
        die('Erreur prepare dans la fonction actionDeleteById');
    }

    if ($idUser == false) {
        die('User not found');
    }else {
        return $idUser[0];
    }
}


function deliver_response($status, $status_message, $data){
    /// Paramétrage de l'entête HTTP, suite
    header("HTTP/1.1 $status $status_message");
    /// Paramétrage de la réponse retournée
    $response['status'] = $status;
    $response['status_message'] = $status_message;
    $response['data'] = $data;
    /// Mapping de la réponse au format JSON
    $json_response = json_encode($response);
    echo $json_response;
}

?>