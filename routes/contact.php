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
 * Overview of contact routes
 *
 * GET -- /user/:uid/contacts
 * retrieve user contact list
 *
 * GET -- /user/:uid/contactgroup/:gid/contacts
 * retrieve user contact list filtered by contact group
 *
 * POST -- /user/:uid/contact
 * create new contact for user
 *
 * PUT -- /user/:uid/contact
 * update user's contact
 *
 * DELETE -- /user/:uid/contact/:cid
 * delete a contact
 */

require_once dirname(__FILE__) . '/../utils.php';
require_once dirname(__FILE__) . '/../models/contact.php';

/**
 * @param int $uid id of logged in user
 * @return array contact list
 */
$app->get(
  '/user/:uid/contacts',
  function($uid) use ($app) {
    try {
      $contacts = \Contact\getAll($uid);

      foreach($contacts as $key => $contact) {
        $groups = \Contact\getAttachedGroups($contact['id']);
        $contacts[$key]['groups'] = $groups;
      }
    } catch (PDOException $e) {
      $app->halt(500, 'Error: ' . $e->getMessage());
    }

    if (($output = \Utils\restResponse($app->response, $contacts)) !== false) {
      echo $output;
    }
  }
)->conditions(array('uid' => '\d+'));

/**
 * @param int $uid id of logged in user
 * @param int $gid contact group id
 * @return array contact list filtered by contact group
 */
$app->get(
  '/user/:uid/contactgroup/:gid/contacts',
  function($uid, $gid) use ($app) {
    try {
      $contacts = \Contact\getContactsByGroup($uid, $gid);

      foreach($contacts as $key => $contact) {
        $groups = \Contact\getAttachedGroups($contact['id']);
        $contacts[$key]['groups'] = $groups;
      }
    } catch (PDOException $e) {
      $app->halt(500, 'Error: ' . $e->getMessage());
    }

    if (($output = \Utils\restResponse($app->response, $contacts)) !== false) {
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
  '/user/:uid/contact',
  function($uid) use ($app) {
    $contact = $app->request->getBody();
    $contact['uid'] = $uid;

    try {
      $cid = \Contact\saveContact($contact);

      if ($cid) $app->response->setStatus(201);
      else $app->response->setStatus(400);
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
  '/user/:uid/contact',
  function($uid) use ($app) {
    $contact = $app->request->getBody();

    try {
      $rowCount = \Contact\updateContact($uid, $contact);

      if ($rowCount) $app->response->setStatus(202);
      else $app->response->setStatus(404);
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
      $rowCount = \Contact\deleteContact($uid, $cid);

      if ($rowCount) $app->response->setStatus(202);
      else $app->response->setStatus(404);
    } catch (PDOException $e) {
      $app->halt(500, 'Error: ' . $e->getMessage());
    }

    $app->response->setStatus(204);
  }
)->conditions(array('uid' => '\d+', 'cid' => '\d+'));

?>
