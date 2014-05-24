<?php
/**
 * Serves single page application and supporting REST API
 *
 * @author Will Shahda <will.shahda@gmail.com
 */

require '../vendor/autoload.php';
require '../utils.php';

$app = new \Slim\Slim();

$app->get('/', function() use ($app) {
  $app->render('index.html');
});

require '../routes/contact.php';

$app->run();

?>
