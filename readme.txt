Exo Atelier

Exercice réalisé en 8h30/9h environ. 
Parties choisies principale + facultative.

L'ensemble des demandes sont fonctinnelles mis à part la dernière demande concernant les communications avec les API 
Le chemin renvoyé n'est jamais bon (voir Vehiculecontroller.php ligne 136).

Présence de CSS réalisé avant l'utilisation de la librairie Boostrap. 
Possibilité de remettre l'ancien CSS en décommentant dans les fichiers base.html.twig et home\index.html.twig.

documentation utilisée pour Boostrap :
https://getbootstrap.com/docs/4.6/getting-started/introduction/

Pour récupérer le projet 

composer install 

puis changer 

DATABASE_URL="mysql://root:rootroot@127.0.0.1:3306/Atelier?serverVersion=8.0.32&charset=utf8mb4"

à changer dans le .env.local

Puis construire la base de donnée avec le .env.local


