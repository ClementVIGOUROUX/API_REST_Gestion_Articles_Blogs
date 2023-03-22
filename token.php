<?php

    include('librairie.php');



    $linkpdo = getConnection();

    $postedData = file_get_contents('php://input');
    $data = json_decode($postedData, true);



    if ($data == null) {
        echo "Connexion non authentifiée acceptée";
    }else {

        $token = actionPostAuth($data['userlogin'], $data['motDePasse'], $linkpdo);


        if (is_jwt_valid($token) == TRUE) {
            echo $token ;
        } else {
            deliver_response(401, "Authentification echoué, votre login ou mot de passe est incorrect", null);
        }
    
    }

    
?>