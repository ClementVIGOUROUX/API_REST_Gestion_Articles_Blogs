<?php

/// Librairies éventuelles (pour la connexion à la BDD, etc.)
 include('librairie.php');

 /// Paramétrage de l'entête HTTP (pour la réponse au Client)
 header("Content-Type:application/json");

$linkpdo = getConnection();

$token = get_bearer_token() ;

if ($token != null) {
    $tokenParts = explode('.', $token);
    $payload = base64_decode($tokenParts[1]);
    
    
    $role = json_decode($payload)->role;
    $role_string = $role->userrole ;
    $user = json_decode($payload)->username;
    $iduser = getIdByUser($user,$linkpdo);
}else {
    $role_string = 'nonauthentifie' ;

}







 /// Identification du type de méthode HTTP envoyée par le client
 $http_method = $_SERVER['REQUEST_METHOD'];
 switch ($http_method){
    /// Cas de la méthode GET
    case "GET" :

        switch($role_string) {

            case "moderator" : 
                    $articles = actionGet($linkpdo);
                    $articles2 = getLikePublisher($linkpdo,$articles);
                    $resultat =getLikeModerateur($linkpdo,$articles2);
                //}

                if ($resultat == null) {
                    deliver_response(404, "Aucun article n'a été trouvé", null);
                } else {
                    /// Envoi de la réponse au Client
                    deliver_response(200, "Requete GET réussie", $resultat);
                }
                break;

            case "publisher" : 
                $postedData = file_get_contents('php://input');
                $data = json_decode($postedData, true);

                if (!empty($data['isAuthor']) && $data['isAuthor'] == true) {
                    $resultat = actionGetMesArticles($linkpdo,$user);
                }else {
                    $articleP = actionGet($linkpdo);
                    $resultat = getLikePublisher($linkpdo,$articleP);
                }
                
                
                if ($resultat == null) {
                    deliver_response(404, "Aucun article n'a été trouvé", null);
                }else {
                    deliver_response(200, "Requete GET réussie", $resultat);
                }
                break;

            case "nonauthentifie" :
                $article = actionGet($linkpdo);
                if ($article == null) {
                    deliver_response(404, "Aucun article n'a été trouvé", null);
                }else {
                    deliver_response(200, "Requete GET réussie", $article);
                }
        }
    
        break;
    /// Cas de la méthode POST
    case "POST" :
    /// Récupération des données envoyées par le Client
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);
        if ($role_string == 'publisher') {
            if (!empty($data['idArticle'])){
                actionPostLikeArticle($data['TypeLike'],$iduser,$data['idArticle'],$linkpdo);
                deliver_response(201, "Requete INSERT LIKE réussie", $data['TypeLike']);
            }else if (!empty($data['contenu'])){
            /// Traitement
            actionPost($data['contenu'], $iduser,  $linkpdo);

            /// Envoi de la réponse au Client
            deliver_response(201, "Requete INSERT ARTICLE réussie", $data['contenu']);
            }else {
                deliver_response(400, "Requete mal formulée", null);
            }
            
        }else {
            deliver_response(401, "Vous ne pouvez pas publier d'article sans avoir le rôle publisher", null);
        }
    
        break;
    /// Cas de la méthode PUT
    case "PUT" :
    /// Récupération des données envoyées par le Client
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);
    /// Traitement
    if ($role_string == 'publisher') {

        if (!empty($data['idArticle']) && !empty($data['contenu'])) {
            actionPutById($data['idArticle'], $data['contenu'], $linkpdo);
            deliver_response(200, "L'article a bien été modifié", $data['contenu']);
        }else {
            deliver_response(400, "Requete mal formulée", null);
        }
        
    }else {
        deliver_response(401, "Vous ne pouvez pas modifier d'article sans avoir le rôle publisher", null) ;
    }
    break;
    /// Cas de la méthode DELETE
    case "DELETE" :

        switch($role_string) {

            case "moderator" : 
                /// Récupération des critères de recherche envoyés par le Client
                if (!empty($_GET['idArticle'])){
                    $resultat = actionDeleteById($_GET['idArticle'], $linkpdo);
                } else {
                    $resultat = null;
                }
                
                if ($resultat == null) {
                    deliver_response(400, "Aucun id d'article n'a ete renseigne", $resultat);
                } else if ($resultat == false){
                    deliver_response(404, "L'article que vous recherchez n'existe pas", $resultat);
                }else {
                    deliver_response(200, "Requete DELETE reussie", $resultat);
                }
            break ;

            case "publisher" :
                /// Récupération des critères de recherche envoyés par le Client
                if (!empty($_GET['idArticle'])){
                    if (isUserAuthor($_GET['idArticle'], $user, $linkpdo) == true) {
                        $resultat = actionDeleteById($_GET['idArticle'], $linkpdo);
                    } else {
                        $resultat = false ;
                    }
                } else {
                    $resultat = null;
                }

                if ($resultat === null) {
                    deliver_response(400, "Aucun id d'article n'a ete renseigne", $resultat);
                }else if ($resultat === false) {
                    deliver_response(403, "L'article que vous souhaitez supprimer ne vous appartient pas", $resultat);
                }else {
                    deliver_response(200, "Requete DELETE reussie", $resultat);
                }

            break ;
        }
        break ;

        
    default :
    }



?>