<?php

include_once __DIR__. '/../../deploy/database.php';
include_once __DIR__. '/../../deploy/constants.php';

$container->setParameter('database_name', ITN_DEPLOY_DBNAME);
$container->setParameter('database_user', ITN_DEPLOY_DBUSER);
$container->setParameter('database_password', ITN_DEPLOY_DBPASS);
