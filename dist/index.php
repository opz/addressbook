<?php
/**
 * Serves single page application and supporting REST API
 *
 * @author Will Shahda <will.shahda@gmail.com
 */

require '../vendor/autoload.php';
require '../utils.php';

$app = new \Slim\Slim();

//Middleware for processing json request data
$app->add(new \Slim\Middleware\ContentTypes());

//Serves single page application front-end
$app->get('/', function() use ($app) {
  $app->render('index.html');
});

require '../routes/contact.php';
require '../routes/user.php';

$app->run();

?>
