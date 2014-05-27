<?php
/**
 * REST API routes for users
 *
 * @author Will Shahda <will.shahda@gmail.com>
 */

/**
 * @param string $email user email login
 * @param string $password user password
 *
 * @return array user attributes if email and password are valid
 */
$app->get('/user', function() use ($app) {
  $user = $app->request->get();

  if (!(isset($user['email']) || isset($user['password']))) {
    $app->response->setStatus(403);
    die();
  }

  $user['password'] = hash('sha512', $user['password']);

  try {
    $dbh = connect();

    $params = array('email', 'password');

    $sql = 'select id, email from users where '
      . implode(' and ', array_map(function($param) { return "$param = :$param"; }, $params));
    $sth = $dbh->prepare($sql);

    bindSetParams($sth, $params, $user);

    $sth->execute();

    $user = $sth->fetch();
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $app->response->setStatus(500);

    die();
  }

  if ($user) $app->response->setStatus(200);
  else $app->response->setStatus(403);

  echo json_encode($user);
});

/**
 * Creates new user
 *
 * @param array $user user attributes
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
