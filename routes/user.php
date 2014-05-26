<?php
/**
 * REST API routes for users
 *
 * @author Will Shahda <will.shahda@gmail.com>
 */

$app->post('/user', function() use ($app) {
  $user = $app->request->getBody();

  if (!isset($user['password'])) {
    $app->response->setStatus(400);
    die();
  }

  $user['password'] = hash('sha512', $user['password']);

  try {
    $dbh = connect();

    $params = array('email', 'password');

    $sql = 'insert into users (' . implode(', ', $params) . ')'
      . ' values (:' . implode(', :', $params) . ')';
    $sth = $dbh->prepare($sql);

    bindSetParams($sth, $params, $user);

    $sth->execute();

    $app->response->setStatus(201);
  } catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    $app->response->setStatus(500);
    die();
  }
});
