<?php
/**
 * REST API routes for contact groups
 *
 * @author Will Shahda <will.shahda@gmail.com>
 */

/**
 * @return array contact group list
 */
$app->get('/user/:uid/contactgroups', function($uid) use ($app) {
  try {
    $dbh = connect();

    $sth = $dbh->prepare('select * from contact_groups where uid = :uid order by created desc');
    $sth->bindParam(':uid', $uid, PDO::PARAM_INT);

    $sth->execute();

    $contactgroups = $sth->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $app->response->setStatus(500);
    die();
  }

  $app->response->setStatus(200);

  echo json_encode($contactgroups);
});

/**
 * Creates new contact group
 *
 * @param array $contactgroup contact group attributes
 */
$app->post('/user/:uid/contactgroup/', function($uid) use ($app) {
  $contactgroup = $app->request->getBody();
  $contactgroup['uid'] = $uid;

  try {
    $dbh = connect();

    $params = array('uid', 'name');

    $sql = 'insert into contact_groups (' . implode(', ', $params) . ')'
      . ' values (:' . implode(', :', $params) . ')';
    $sth = $dbh->prepare($sql);

    bindSetParams($sth, $params, $contactgroup);

    $sth->execute();

    $app->response->setStatus(201);
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      $app->response->setStatus(500);
      die();
  }
});

/**
 * Attaches contact group to contact
 *
 * @param array $contactgroup contact group attributes
 */
$app->post('/user/:uid/contact/:cid/contactgroup/', function($uid, $cid) use ($app) {
  $contactgroup = $app->request->getBody();

  try {
    $dbh = connect();

    $sql = 'insert into contact_group_jct (cid, gid) values (:cid, :gid)';
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':cid', $cid, PDO::PARAM_INT);
    $sth->bindParam(':gid', $contactgroup['id'], PDO::PARAM_INT);

    $sth->execute();
    $gid = $dbh->lastInsertId();

    $app->response->setStatus(201);
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      $app->response->setStatus(500);
      die();
  }

  if ($gid) echo json_encode(array('id' => $gid));
});

/**
 * Update existing contact group
 *
 * @param array $contactgroup contact group attributes
 */
$app->put('/user/:uid/contactgroup/', function($uid) use ($app) {
  $contactgroup = $app->request->getBody();

  try {
    $dbh = connect();

    $params = array('id', 'uid', 'name');

    $sql = 'update contact_groups set name = :name where id = :id and uid = :uid';
    $sth = $dbh->prepare($sql);

    bindSetParams($sth, $params, $contactgroup);

    $sth->execute();

    $app->response->setStatus(201);
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      $app->response->setStatus(500);
      die();
  }
});

/**
 * Deletes contact group
 *
 * @param int $gid contact group id
 */
$app->delete('/user/:uid/contactgroup/:gid', function($uid, $gid) use ($app) {
  if ($gid) {
    try {
      $dbh = connect();

      $sql = 'delete from contact_groups where id = ? and uid = ?';
      $sth = $dbh->prepare($sql);

      $sth->execute(array($gid, $uid));
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      $app->response->setStatus(500);
      die();
    }

    $app->response->setStatus(200);
  }
});

/**
 * Detach contact group from contact
 *
 * @param int $gid contact group id
 */
$app->delete('/user/:uid/contact/:cid/contactgroup/:gid', function($uid, $cid, $gid) use ($app) {
  try {
    $dbh = connect();

    $sql = 'delete from contact_group_jct where cid = ? and gid = ?';
    $sth = $dbh->prepare($sql);

    $sth->execute(array($cid, $gid));
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $app->response->setStatus(500);
    die();
  }

  $app->response->setStatus(200);
});

?>
