<?php

/// Librairies éventuelles (pour la connexion à la BDD, etc.)
 include('librairie.php');

 /// Paramétrage de l'entête HTTP (pour la réponse au Client)
 header("Content-Type:application/json");

$linkpdo = getConnection();

$token = get_bearer_token() ;

$tokenParts = explode('.', $token);
$payload = base64_decode($tokenParts[1]);

$role = json_decode($payload)->role;

 /// Identification du type de méthode HTTP envoyée par le client
 $http_method = $_SERVER['REQUEST_METHOD'];
 switch ($http_method){
    /// Cas de la méthode GET
    case "GET" :
    /// Récupération des critères de recherche envoyés par le Client
        if (!empty($_GET['idArticle'])){

            $resultat = actionGetById($_GET['idArticle'], $linkpdo);
        } else {
            $resultat = actionGet($linkpdo);
        }
        /*
        //POUR FAIRE D'AUTRES GET
        if (!empty($_GET['?'])) {
            
        }
        */

        if ($resultat == null) {
            deliver_response(404, "La ressource que vous recherchez n'existe pas",null);
        } else {
            /// Envoi de la réponse au Client
            deliver_response(200, "Requete réussie", $resultat);
        }
        break;
    /// Cas de la méthode POST
    case "POST" :
    /// Récupération des données envoyées par le Client
    $postedData = file_get_contents('php://input');
    $data = json_decode($postedData, true);
    /// Traitement
    actionPost($data['contenu'], $data['idUtilisateur'],  $linkpdo);

    /// Envoi de la réponse au Client
    deliver_response(201, "Requete INSERT réussie", $data['contenu']);
    break;
    /// Cas de la méthode PUT
    case "PUT" :
    /// Récupération des données envoyées par le Client
    $postedData = file_get_contents('php://input');
    $data = json_decode($postedData, true);
    var_dump($data);
    /// Traitement

    actionPutById($data['id'], $data['phrase'], $linkpdo);

    /// Envoi de la réponse au Client
    deliver_response(200, "Votre message", NULL);
    break;
    /// Cas de la méthode DELETE
    case "DELETE" :
    /// Récupération de l'identifiant de la ressource envoyé par le Client
    if (!empty($_GET['id'])){
        actionDeleteById($_GET['id'], $linkpdo);
    }
    /// Envoi de la réponse au Client
    deliver_response(200, "Votre message", $_GET['id']);
    break;
    default :

}


/// Envoi de la réponse au Client
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