livretO
=======

A Symfony project created on January 9, 2017, 4:40 pm.

Pré-requis :
- Apache2
- PHP7
- PHP - MySQL
- PHP - ldap
- PHP - dev

Pour installer l'application :
- Fork et clone du projet https://victor.maupu@gitlab.com/victor.maupu/projet_livretO_2A.git

Pour vérifier que symfony est prêt à fonctionner sur la machine :

> php bin/symfony_requirements

OU

> http://localhost:8000/config.php

Mise à jour des paquets additionnels 

> php composer.phar update ( cela peut prendre quelque minute) 

- Relancer le server 


Si la base de données n'est pas instalée :

> php bin/console doctrine:database:create

> php bin/console doctrine:schema:create

> php bin/console doctrine:schema:validate
- Relancer le serveur

Lancement et arrêt du serveur :

> php bin/console server:start    (pour lancer le projet en fond)

> php bin/console server:run      (pour lancer le serveur et voir les requetes serveur)
 
> php bin/console server:stop     (pour stopper le serveur)

- http://localhost:8000/


