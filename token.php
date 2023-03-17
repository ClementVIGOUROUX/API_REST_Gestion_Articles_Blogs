<?php

    $linkpdo = getConnection();

    $postedData = file_get_contents('php://input');
    $data = json_decode($postedData, true);

    if ($data['login'] == null && $data['motDePasse'] == null) {
        echo "Connexion non authentifiée acceptée";
    }

    $token = actionPostAuth($data['login'], $data['motDePasse'], $linkpdo);

    if (is_jwt_valid($token, $secret = 'monsecret') == TRUE) {
        echo $token ;
    } else {
        deliver_response(401, "Authentification echoué, votre login ou mot de passe est incorrect", null);
    }

?>