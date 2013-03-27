LinkShortener
==========

LinkShortener est un raccourcisseur d'URL avec statistiques intégrées :
 - Nombres de clics
 - Nombres de visiteurs par jour
 - Sites référents 
 - Pays des visiteurs


<h2>Wiki d'installation</H2>

- Vérifier votre configuration :<br />
<code>
php app/check.php
</code>

- Mettre à jours les vendors via composer :<br />
<code>
curl -s https://getcomposer.org/installer | php
</code>
<br />
<code>
php composer.phar update
</code>

- Créer la base de donnée (si ça n'est pas déjà fait)
<code>
php app/console doctrine:database:create
</code>

- Mettre à jours la base de donnée
<code>
php app/console doctrine:schema:update --force
</code>

=========================
Travailler en app_dev.php 
=========================
