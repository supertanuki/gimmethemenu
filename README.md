Gimme The Menu
========================

h1. Installation

* Recaptcha keys
in /app/config.php
for ewz_recaptcha parameters
Create keys for prod domain @ https://www.google.com/recaptcha/admin#createsite

* Change status to closed of published projects which have dateEnd < now
cronjob to run every hour :
$ php app/console project:close-ended


h1. Access

Accès QA :
* URL : http://tendances-edition-qa.itnetwork.fr
* Identifiant : tendances
* Mot de passe : edition

Tu peux t'enregistrer en tant qu'utilisateur pour pouvoir consulter les projets et y répondre.

Accès à l'administration :
* http://tendances-edition-qa.itnetwork.fr/_administration
* Identifiant : myadmin
* Mot de passe : mypass


h1. Tips

* Create child admin
http://sonata-project.org/bundles/admin/master/doc/reference/architecture.html#create-child-admins

* Redirecting on login/logout in Symfony2 using LoginHandlers.
http://www.reecefowell.com/2011/10/26/redirecting-on-loginlogout-in-symfony2-using-loginhandlers/
or http://symfony.com/doc/current/cookbook/security/target_path.html
or http://symfony.com/fr/doc/current/cookbook/security/form_login.html#controler-l-url-de-redirection-depuis-le-formulaire
