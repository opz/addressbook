<?php
/**
 * REST API routes for contacts
 *
 * @author Will Shahda <will.shahda@gmail.com
 */

$app->get('/contacts', function() use ($app) {
  try {
    $dbh = connect();

    $sth = $dbh->prepare('select * from contacts');
    $sth->execute();

    $contacts = $sth->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $app->response->setStatus(500);

    die();
  }

  $app->response->setStatus(200);

  echo json_encode($contacts);

});

$app->get('/contact/:cid', function($cid) use ($app) {
  try {
    $dbh = connect();

    $sth = $dbh->prepare('select * from contacts where id = ?');
    $sth->bindParam(1, $cid);
    $sth->execute();
    $contact = $sth->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $app->response->setStatus(500);

    die();
  }

  $app->response->setStatus(200);

  echo json_encode($contact);
});

$app->post('/contact/', function() use ($app) {
  $contact = $app->request->getBody();

  try {
    $dbh = connect();

    $params = array('first', 'last', 'email',
      'address', 'phone', 'notes');

    $sql = 'insert into contacts (' . implode(', ', $params) . ')'
      . 'values (:' . implode(', :', $params) . ')';
    $sth = $dbh->prepare($sql);

    bindSetParams($sth, $params, $contact);

    $sth->execute();

    $app->response->setStatus(201);
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      $app->response->setStatus(500);

      die();
  }
});

$app->delete('/contact/:id', function($id) use ($app) {
  if ($id) {
    try {
      $dbh = connect();

      $sql = 'delete from contacts where id = ?';
      $sth = $dbh->prepare($sql);
      $sth->execute(array($id));
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      $app->response->setStatus(500);

      die();
    }

    $app->response->setStatus(200);
  }
});

?>
