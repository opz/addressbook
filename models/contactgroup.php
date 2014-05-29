<?php
/**
 * Contact group model
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

namespace ContactGroup;

require_once dirname(__FILE__) . '/../utils.php';

/**
 * @param int $uid id of logged in user
 * @return array contact group list
 */
function getAll($uid) {
  $dbh = \Utils\connect();

  $sth = $dbh->prepare('select * from contact_groups where uid = :uid order by created desc');
  $sth->bindParam(':uid', $uid, \PDO::PARAM_INT);

  $sth->execute();

  $contactgroups = $sth->fetchAll(\PDO::FETCH_ASSOC);

  return $contactgroups;
}

/**
 * @param int $uid id of logged in user
 * @return int id of new contact group
 */
function saveContactGroup($uid, $contactgroup) {
  $dbh = \Utils\connect();

  $params = array('uid', 'name');

  $sql = 'insert into contact_groups (' . implode(', ', $params) . ')'
    . ' values (:' . implode(', :', $params) . ')';
  $sth = $dbh->prepare($sql);

  $contactgroup['uid'] = $uid;

  \Utils\bindSetParams($sth, $params, $contactgroup);

  $sth->execute();
  $gid = $dbh->lastInsertId();

  return $gid;
}

/**
 * @param int $uid id of logged in user
 * @param int $cid contact id
 * @param array $contactgroup contact group to attach to contact
 * @return int id of new contact group junction on success, false on failure
 */
function attachGroupToContact($uid, $cid, $contactgroup) {
  $dbh = \Utils\connect();

  $sql = 'select id from contact_group_jct where cid = :cid and gid = :gid';
  $sth = $dbh->prepare($sql);
  $sth->bindParam(':cid', $cid, \PDO::PARAM_INT);
  $sth->bindParam(':gid', $contactgroup['id'], \PDO::PARAM_INT);

  $sth->execute();

  $row = $sth->fetch(\PDO::FETCH_ASSOC);

  if ($row === false || count($row) === 0) {
    $sql = 'insert into contact_group_jct (cid, gid) values (:cid, :gid)';
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':cid', $cid, \PDO::PARAM_INT);
    $sth->bindParam(':gid', $contactgroup['id'], \PDO::PARAM_INT);

    $sth->execute();

    return $dbh->lastInsertId();
  }

  return false;
}

/**
 * @param int $uid id of logged in user
 * @param array $contactgroup contact group attributes to update
 * @return int number of rows updated
 */
function updateContactGroup($uid, $contactgroup) {
  $dbh = \Utils\connect();

  $params = array('id', 'uid', 'name');

  $sql = 'update contact_groups set name = :name where id = :id and uid = :uid';
  $sth = $dbh->prepare($sql);

  \Utils\bindSetParams($sth, $params, $contactgroup);

  $sth->execute();

  return $sth->rowCount();
}

/**
 * @param int $uid if of logged in user
 * @param int $gid contact group id
 * @return int number of rows deleted
 */
function deleteContactGroup($uid, $gid) {
  $dbh = \Utils\connect();

  $sql = 'delete from contact_groups where id = ? and uid = ?';
  $sth = $dbh->prepare($sql);

  $sth->execute(array($gid, $uid));

  return $sth->rowCount();
}

/**
 * @param int $cid contact id
 * @param int $gid contact group id
 * @return int number of rows deleted
 */
function detachGroupFromContact($cid, $gid) {
  $dbh = \Utils\connect();

  $sql = 'delete from contact_group_jct where cid = ? and gid = ?';
  $sth = $dbh->prepare($sql);

  $sth->execute(array($cid, $gid));

  return $sth->rowCount();
}

?>
