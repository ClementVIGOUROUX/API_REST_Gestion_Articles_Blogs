<h1>Projet : Conception et développement d’API REST pour la gestion d’articles</h1>

<h3>Description</h3>

L’objectif de ce projet est de proposer une solution pour la gestion d'articles de blog. 
Le projet a été réalisé en binôme par VIGOUROUX Clément et NOGUERO Vincent.

<h5>Spécifications fonctionnelles</h5>
• Publier, consulter, modifier et supprimer des articles : Un article est défini par sa date de publication, son auteur et son contenu.


• Authentification des utilisateurs : L'authentification est requise pour les utilisateurs qui souhaitent interagir avec les articles. Elle doit être basée sur des         jetons Web JSON (JWT). Un utilisateur est défini par un nom d'utilisateur, un mot de passe et un rôle (moderateur ou publisher).

• Liker/Disliker un article : Les utilisateurs peuvent aimer ou ne pas aimer un article, et la solution doit permettre de récupérer les données des utilisateurs pour       chaque article.

<h5>Gestion des restrictions d'accès</h5>
Des restrictions d'accès doivent être mises en place pour la gestion des articles en fonction du rôle de l'utilisateur, comme suit :

• Modérateur : Un modérateur peut consulter n'importe quel article et accéder à toutes les informations relatives à un article, y compris l'auteur, la date de           publication, le contenu, la liste des utilisateurs qui ont aimé l'article, le nombre total d'articles aimés, la liste des utilisateurs qui n'ont pas aimé l'article et le nombre total d'articles non aimés. Un modérateur peut également supprimer un article.
• Publisher : Un éditeur peut publier un nouvel article, consulter ses propres articles, accéder aux informations relatives aux articles des autres utilisateurs (notamment l'auteur, la date de publication, le contenu, le nombre total de "j'aime" et le nombre total de "je n'aime pas"), modifier les articles qu'il a rédigés, supprimer ses propres articles et aimer/désaimer les articles des autres utilisateurs.
• Utilisateur non authentifié : Un utilisateur non authentifié ne peut que consulter les articles existants et accéder à des informations limitées, notamment l'auteur, la date de publication et le contenu.

