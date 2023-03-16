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


function actionGet($linkpdo) {
    $query = $linkpdo->query('SELECT * FROM article');

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

//A faire pour le jeton

function isValidUser($userlogin, $userpassword,  $linkpdo) {
    $query = $linkpdo->query('SELECT u.motDePasse FROM utilisateur u WHERE login = '. $userlogin);
    $row = $query->fetch();
    return ($row == $userpassword);
}





function actionPostAuth($userlogin, $userpassword, $linkpdo) {

    if (isValidUser($userlogin, $userpassword, $linkpdo)) {

        $query = $linkpdo->query('SELECT u.role FROM utilisateur u WHERE login = '. $userlogin);
        $role = $query->fetch();

        $headers = array('alg' => 'HS256', 'typ' => 'JWT');
        $payload = array('username' => $userlogin, 'role' => $role, 'exp' =>(time() + 60));
        //Création du token
        $token = generate_jwt($headers, $payload);

        return $token ;

    }



}


?>