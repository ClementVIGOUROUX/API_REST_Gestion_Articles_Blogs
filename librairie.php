<?php

 include('jwt_utils.php');

function getConnection() {

    $server="localhost";
    $login="root";
    $mdp='$iutinfo';
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

#pas utile
function getIdByUsername($username, $linkpdo) {
    $query = $linkpdo->query('SELECT idUtilisateur FROM utilisateur WHERE login = ' . $username);

    if ($query == false) {
        die('Erreur query dans la fonction getIdByUsername');
    }

    $id = $query->fetch();
    return $id ;

}


function actionGetById($idArticle,$linkpdo) {
    $query = $linkpdo->query('SELECT * FROM article WHERE idArticle ='. $idArticle);

    if ($query == false) {
        die('Erreur query dans la fonction actionGetById');
    }

    while($donnees = $query->fetch(PDO::FETCH_ASSOC)) {
        $articles[] = $donnees;
    }
    return $articles ;

}


function actionGetArticlesByUser($idUtilisateur, $linkpdo) {
    $query = $linkpdo->query('SELECT * FROM article WHERE idUtilisateur ='. $idUtilisateur);


    if ($query == false) {
        die('Erreur query dans la fonction actionGetArticlesByUser');
    }

    while($donnees = $query->fetch(PDO::FETCH_ASSOC)) {
        $articles[] = $donnees;
    }
    return $articles ;

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
    $query = $linkpdo->query('DELETE FROM article WHERE idArticle ='. $id);

    if ($query == false) {
        die('Erreur prepare dans la fonction actionDeleteById');
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

        $headers = array('alg' => 'HS256', 'typ' => 'JWT');
        $payload = array('username' => $userlogin, 'role' => $role, 'exp' =>(time() + 60));
        //Création du token
        $token = generate_jwt($headers, $payload);

        return $token ;

    } else {
        return FALSE;
    }



}

function actionGetArticlePublisher($linkpdo){
    $query = $linkpdo->query('SELECT nomAuteur as "Auteur" , contenu  , datePublication as "Date de Publication",IdArticle FROM article INNER JOIN utilisateur on article.idUtilisateur = utilisateur.idUtilisateur ORDER BY 3');
    if ($query == false) {
        die('Erreur query dans la fonction actionGet');
    }

    while($donnees2 = $query->fetch(PDO::FETCH_ASSOC)) {
        $articles[] = $donnees2;
    }

    foreach ($articles as $article){
        $idArticle = $article['IdArticle'];
        $query2 = $linkpdo->query('SELECT TypeLike , count(TypeLike) as "Nombre de Like" FROM aimer WHERE idArticle ='. $idArticle .' GROUP BY(TypeLike)');
        while($donnees3 = $query2->fetch(PDO::FETCH_ASSOC)) {
            $likes[] = $donnees3;
        }

    }
    print_r($likes);
    print_r($articles);
    return $articles;
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


function getLike($linkpdo , $article){
    foreach ($article as $art){
        $query = $linkpdo->query('SELECT idArticle,TypeLike , COUNT(TypeLike) from aimer where idArticle='. $art['idArticle'].' GROUP By(`TypeLike`);');
        if ($query == false) {
            die('Erreur query dans la fonction getLike');
        }
        while($donnees3 = $query->fetch(PDO::FETCH_ASSOC)) {
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
$linkpdo = getConnection();
$a = actionGet($linkpdo);
print_r(getLike($linkpdo,$a));



?>