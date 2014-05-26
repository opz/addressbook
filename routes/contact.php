<?php
/**
 * REST API routes for contacts
 *
 * @author Will Shahda <will.shahda@gmail.com>
 */

/**
 * @return array contact list
 */
$app->get('/contacts', function() use ($app) {
  try {
    $dbh = connect();

    $sth = $dbh->prepare('select * from contacts order by created desc');
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

/**
 * @param int $cid contact id
 *
 * @return array contact attributes 
 */
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

/**
 * Creates new contact
 *
 * @param array $contact contact attributes
 */
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

/**
 * Deletes contact
 *
 * @param int $cid contact id
 */
$app->delete('/contact/:cid', function($cid) use ($app) {
  if ($cid) {
    try {
      $dbh = connect();

      $sql = 'delete from contacts where id = ?';
      $sth = $dbh->prepare($sql);
      $sth->execute(array($cid));
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      $app->response->setStatus(500);

      die();
    }

    $app->response->setStatus(200);
  }
});

?>
