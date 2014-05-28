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

    //select contact list
    $sql = 'select * from contacts '
      . 'where uid = :uid order by created desc';
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':uid', $uid, PDO::PARAM_INT);

    $sth->execute();

    $contacts = $sth->fetchAll(PDO::FETCH_ASSOC);

    foreach($contacts as $key => $contact) {
      $sql = 'select * from contact_group_jct '
        . 'join contact_groups on gid = contact_groups.id '
        . 'where cid = :cid order by created desc';
      $sth = $dbh->prepare($sql);
      $sth->bindParam(':cid', $contact['id'], PDO::PARAM_INT);

      $sth->execute();

      $groups = $sth->fetchAll(PDO::FETCH_ASSOC);
      $contacts[$key]['groups'] = $groups;
    }
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $app->response->setStatus(500);
    die();
  }

  $app->response->setStatus(200);

  echo json_encode($contacts);
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

    //insert contact
    $params = array('uid', 'first', 'last', 'email',
      'address', 'phone', 'notes');

    $sql = 'insert into contacts (' . implode(', ', $params) . ')'
      . ' values (:' . implode(', :', $params) . ')';
    $sth = $dbh->prepare($sql);

    bindSetParams($sth, $params, $contact);

    $sth->execute();
    $cid = $dbh->lastInsertId();

    $app->response->setStatus(201);
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      $app->response->setStatus(500);
      die();
  }

  if ($cid) echo json_encode(array('id' => $cid));
});

/**
 * Update existing contact
 *
 * @param array $contact contact attributes
 */
$app->put('/user/:uid/contact/', function($uid) use ($app) {
  $contact = $app->request->getBody();

  try {
    $dbh = connect();

    $params = array('first', 'last', 'email',
      'address', 'phone', 'notes');

    $sql = 'update contacts set '
      . implode(', ', array_map(function($param) {
        return "$param = :$param";
      }, $params))
      . ' where id = :id and uid = :uid';
    $sth = $dbh->prepare($sql);

    $params[] = 'id';
    $params[] = 'uid';

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
