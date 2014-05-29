<?php
/**
 * REST API routes for contacts
 *
 * @author Will Shahda <will.shahda@gmail.com>
 * @copyright 2014 Will Shahda
 * @package addressbook
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Will Shahda
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */

/**
 * @param int $uid id of logged in user
 *
 * @return array contact list
 */
$app->get(
  '/user/:uid/contacts',
  function($uid) use ($app) {
    try {
      $dbh = connect();

      //select contact list
      $sql = 'select '
        . 'id, first, last, email, address, phone, notes, created '
        . 'from contacts '
        . 'where uid = :uid order by created desc';
      $sth = $dbh->prepare($sql);
      $sth->bindParam(':uid', $uid, PDO::PARAM_INT);

      $sth->execute();

      $contacts = $sth->fetchAll(PDO::FETCH_ASSOC);

      foreach($contacts as $key => $contact) {
        $groups = getAttachedGroups($dbh, $contact['id']);
        $contacts[$key]['groups'] = $groups;
      }
    } catch (PDOException $e) {
      $app->halt(500, 'Error: ' . $e->getMessage());
    }

    if (($output = restResponse($app->response, $contacts)) !== false) {
      echo $output;
    }
  }
)->conditions(array('uid' => '\d+'));

/**
 * @param int $uid id of logged in user
 * @param int $gid contact group id
 *
 * @return array contact list filtered by contact group
 */
$app->get(
  '/user/:uid/contactgroup/:gid/contacts',
  function($uid, $gid) use ($app) {
    try {
      $dbh = connect();

      $sql = 'select '
        . 'cid as id, first, last, email, address, phone, notes, contacts.created '
        . 'from contact_group_jct '
        . 'join contacts on contacts.id = cid '
        . 'where uid = :uid and gid = :gid order by contacts.created desc';
      $sth = $dbh->prepare($sql);
      $sth->bindParam(':uid', $uid, PDO::PARAM_INT);
      $sth->bindParam(':gid', $gid, PDO::PARAM_INT);

      $sth->execute();

      $contacts = $sth->fetchAll(PDO::FETCH_ASSOC);

      foreach($contacts as $key => $contact) {
        $groups = getAttachedGroups($dbh, $contact['id']);
        $contacts[$key]['groups'] = $groups;
      }
    } catch (PDOException $e) {
      $app->halt(500, 'Error: ' . $e->getMessage());
    }

    if (($output = restResponse($app->response, $contacts)) !== false) {
      echo $output;
    }
  }
)->conditions(array('uid' => '\d+', 'gid' => '\d+'));

/**
 * Creates new contact
 *
 * @param int $uid id of logged in user
 * @param array $contact contact attributes
 */
$app->post(
  '/user/:uid/contact/',
  function($uid) use ($app) {
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
      $app->halt(500, 'Error: ' . $e->getMessage());
    }

    if ($cid) echo json_encode(array('id' => $cid));
  }
)->conditions(array('uid' => '\d+'));

/**
 * Update existing contact
 *
 * @param int $uid id of logged in user
 * @param array $contact contact attributes
 */
$app->put(
  '/user/:uid/contact/',
  function($uid) use ($app) {
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
      $app->halt(500, 'Error: ' . $e->getMessage());
    }
  }
)->conditions(array('uid' => '\d+'));

/**
 * Deletes contact
 *
 * @param int $uid id of logged in user
 * @param int $cid contact id
 */
$app->delete(
  '/user/:uid/contact/:cid',
  function($uid, $cid) use ($app) {
    try {
      $dbh = connect();

      $sql = 'delete from contacts where id = ? and uid = ?';
      $sth = $dbh->prepare($sql);

      $sth->execute(array($cid, $uid));
    } catch (PDOException $e) {
      $app->halt(500, 'Error: ' . $e->getMessage());
    }

    $app->response->setStatus(204);
  }
)->conditions(array('uid' => '\d+', 'cid' => '\d+'));

function getAttachedGroups($dbh, $cid) {
  $sql = 'select '
    . 'gid as id, name, contact_group_jct.created as created '
    . 'from contact_group_jct '
    . 'join contact_groups on gid = contact_groups.id '
    . 'where cid = :cid order by contact_group_jct.created desc';

  $sth = $dbh->prepare($sql);
  $sth->bindParam(':cid', $cid, PDO::PARAM_INT);

  $sth->execute();

  $groups = $sth->fetchAll(PDO::FETCH_ASSOC);

  return $groups;
}

?>
