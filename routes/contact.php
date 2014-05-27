<?php
/**
 * REST API routes for contacts
 *
 * @author Will Shahda <will.shahda@gmail.com>
 */

/**
 * @return array contact list
 */
$app->get('/user/:uid/contacts', function($uid) use ($app) {
  try {
    $dbh = connect();

    $sth = $dbh->prepare('select * from contacts where uid = :uid order by created desc');
    $sth->bindParam(':uid', $uid, PDO::PARAM_INT);

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
$app->post('/user/:uid/contact/', function($uid) use ($app) {
  $contact = $app->request->getBody();
  $contact['uid'] = $uid;

  try {
    $dbh = connect();

    $params = array('uid', 'first', 'last', 'email',
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
$app->delete('/user/:uid/contact/:cid', function($uid, $cid) use ($app) {
  if ($cid) {
    try {
      $dbh = connect();

      $sql = 'delete from contacts where id = ? and uid = ?';
      $sth = $dbh->prepare($sql);

      $sth->execute(array($cid, $uid));
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      $app->response->setStatus(500);
      die();
    }

    $app->response->setStatus(200);
  }
});

?>
