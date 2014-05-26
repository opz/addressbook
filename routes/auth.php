<?php
/**
 * REST API routes for authentication
 *
 * @author Will Shahda <will.shahda@gmail.com>
 */

$app->get('/auth', function() use ($app) {
  $user = $app->request->getBody();

  try {
    $dbh = connect();

    $params = array('email', 'password');

    $sql = 'select * from users '
      . 'where email = :email '
      . 'and password = :password';
    $sth = $dbh->prepare($sql);

    bindSetParams($sth, $params, $user);

    $sth->execute();

    if ($user = $sth->fetch()) $app->response->setStatus(200);
    else $app->response->setStatus(401);
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $app->response->setStatus(500);

    die();
  }
}

$app->post('/auth', function() use ($app) {
});

?>
