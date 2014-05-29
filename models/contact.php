<?php
/**
 * Contact model
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

require_once dirname(__FILE__) . '/../utils.php';

/**
 * Contact
 *
 * This class is not a true model, more just a collection
 * of useful helper methods.
 */
class Contact {
  /**
   * @param int $uid id of logged in user
   * @return array contact list
   */
  public static function getAll($uid) {
    $dbh = Utils::connect();

    $sql = 'select '
      . 'id, first, last, email, address, phone, notes, created '
      . 'from contacts '
      . 'where uid = :uid order by created desc';
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':uid', $uid, PDO::PARAM_INT);

    $sth->execute();

    $contacts = $sth->fetchAll(PDO::FETCH_ASSOC);

    return $contacts;
  }

  /**
   * @param int $uid id of logged in user
   * @param int $gid contact group id
   * @return array contact list filtered by contact group
   */
  public static function getContactsByGroup($uid, $gid) {
    $dbh = Utils::connect();

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

    return $contacts;
  }

  /**
   * @param int $cid contact id
   * @return array list of groups attached to contact
   */
  public static function getAttachedGroups($cid) {
    $dbh = Utils::connect();

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

  /**
   * @param array $contact contact attributes to save
   * @return int id of new contact
   */
  public static function saveContact($contact) {
    $dbh = Utils::connect();

    $params = array('uid', 'first', 'last', 'email',
      'address', 'phone', 'notes');

    $sql = 'insert into contacts (' . implode(', ', $params) . ')'
      . ' values (:' . implode(', :', $params) . ')';
    $sth = $dbh->prepare($sql);

    Utils::bindSetParams($sth, $params, $contact);

    $sth->execute();
    $cid = $dbh->lastInsertId();

    return $cid;
  }

  /**
   * @param array $contact contact attributes to update
   * @return int number of rows updated
   */
  public static function updateContact($uid, $contact) {
    $dbh = Utils::connect();

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
    $contact['uid'] = $uid;

    Utils::bindSetParams($sth, $params, $contact);

    $sth->execute();

    return $sth->rowCount();
  }

  public static function deleteContact($uid, $cid) {
    $dbh = Utils::connect();

    $sql = 'delete from contacts where id = ? and uid = ?';
    $sth = $dbh->prepare($sql);

    $sth->execute(array($cid, $uid));

    return $sth->rowCount();
  }
}

?>
