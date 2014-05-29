<?php
/**
 * REST API routes for contact groups
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
 * Overview of contact group routes
 *
 * GET -- /user/:uid/contactgroups
 * retrieve user contact groups
 *
 * POST -- /user/:uid/contactgroup
 * create new contact group for user
 *
 * POST -- /user/:uid/contact/:cid/contactgroup
 * attach contact group to a users contact
 *
 * PUT -- /user/:uid/contactgroup
 * update contact group
 *
 * DELETE -- /user/:uid/contactgroup/:gid
 * delete contact group
 *
 * DELETE -- /user/:uid/contact/:cid/contactgroup/:gid
 * detach a contact group from a users contact
 */

require_once dirname(__FILE__) . '/../utils.php';
require_once dirname(__FILE__) . '/../models/contactgroup.php';

/**
 * @param int $uid id of logged in user
 * @return array contact group list
 */
$app->get(
  '/user/:uid/contactgroups',
  function($uid) use ($app) {
    try {
      $contactgroups = ContactGroup::getAll($uid);
    } catch (PDOException $e) {
      $app->halt(500, 'Error: ' . $e->getMessage());
    }

    if (($output = \Utils\restResponse($app->response, $contactgroups)) !== false) {
      echo $output;
    }
  }
)->conditions(array('uid' => '\d+'));

/**
 * Creates new contact group
 *
 * @param int $uid id of logged in user
 * @param array $contactgroup contact group attributes
 */
$app->post(
  '/user/:uid/contactgroup',
  function($uid) use ($app) {
    $contactgroup = $app->request->getBody();

    try {
      $gid = ContactGroup::saveContactGroup($uid, $contactgroup);

      if ($gid) $app->response->setStatus(201);
      else $app->response->setStatus(400);
    } catch (PDOException $e) {
      $app->halt(500, 'Error: ' . $e->getMessage());
    }

    if ($gid) echo json_encode(array('id' => $gid));
  }
)->conditions(array('uid' => '\d+'));

/**
 * Attaches contact group to contact
 *
 * @param int $uid id of logged in user
 * @param int $cid id of selected contact
 * @param array $contactgroup contact group attributes
 */
$app->post(
  '/user/:uid/contact/:cid/contactgroup',
  function($uid, $cid) use ($app) {
    $contactgroup = $app->request->getBody();

    try {
      $cgid = ContactGroup::attachGroupToContact($uid, $cid, $contactgroup);

      if ($cgid) $app->response->setStatus(201);
      else $app->response->setStatus(405);
    } catch (PDOException $e) {
      $app->halt(500, 'Error: ' . $e->getMessage());
    }

    if ($cgid) echo json_encode(array('id' => $cgid));
  }
)->conditions(array('uid' => '\d+', 'cid' => '\d+'));

/**
 * Update existing contact group
 *
 * @param int $uid id of logged in user
 * @param array $contactgroup contact group attributes
 */
$app->put(
  '/user/:uid/contactgroup',
  function($uid) use ($app) {
    $contactgroup = $app->request->getBody();

    try {
      $dbh = \Utils\connect();

      $params = array('id', 'uid', 'name');

      $sql = 'update contact_groups set name = :name where id = :id and uid = :uid';
      $sth = $dbh->prepare($sql);

      \Utils\bindSetParams($sth, $params, $contactgroup);

      $sth->execute();

      $app->response->setStatus(201);
    } catch (PDOException $e) {
      $app->halt(500, 'Error: ' . $e->getMessage());
    }
  }
)->conditions(array('uid' => '\d+'));

/**
 * Deletes contact group
 *
 * @param int $uid id of logged in user
 * @param int $gid contact group id
 */
$app->delete(
  '/user/:uid/contactgroup/:gid',
  function($uid, $gid) use ($app) {
    try {
      $dbh = \Utils\connect();

      $sql = 'delete from contact_groups where id = ? and uid = ?';
      $sth = $dbh->prepare($sql);

      $sth->execute(array($gid, $uid));
    } catch (PDOException $e) {
      $app->halt(500, 'Error: ' . $e->getMessage());
    }

    $app->response->setStatus(204);
  }
)->conditions(array('uid' => '\d+', 'gid' => '\d+'));

/**
 * Detach contact group from contact
 *
 * @param int $uid id of logged in user
 * @param int $cid id of selected contact
 * @param int $gid contact group id
 */
$app->delete(
  '/user/:uid/contact/:cid/contactgroup/:gid',
  function($uid, $cid, $gid) use ($app) {
    try {
      $dbh = \Utils\connect();

      $sql = 'delete from contact_group_jct where cid = ? and gid = ?';
      $sth = $dbh->prepare($sql);

      $sth->execute(array($cid, $gid));
    } catch (PDOException $e) {
      $app->halt(500, 'Error: ' . $e->getMessage());
    }

    $app->response->setStatus(204);
  }
)->conditions(array('uid' => '\d+', 'cid' => '\d+', 'gid' => '\d+'));

?>
