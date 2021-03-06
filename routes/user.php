<?php
/**
 * REST API routes for users
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
 * Overview of user routes
 *
 * GET -- /user
 * retrieve user if valid login
 *
 * POST -- /user
 * create new user
 */

require_once dirname(__FILE__) . '/../utils.php';
require_once dirname(__FILE__) . '/../models/user.php';

/**
 * @param string $email user email login
 * @param string $password user password
 * @return array user attributes if email and password are valid
 */
$app->get('/user', function() use ($app) {
  $user = $app->request->get();

  if (!(isset($user['email']) || isset($user['password']))) {
    $app->halt(403);
  }

  $user['password'] = hash('sha512', $user['password']);

  try {
    $user = \User\getUser($user);
  } catch (PDOException $e) {
    $app->halt(500, 'Error: ' . $e->getMessage());
  }

  if ($user) $app->response->setStatus(200);
  else $app->response->setStatus(403);

  echo json_encode($user);
});

/**
 * Creates new user
 *
 * @param array $user user attributes
 */
$app->post('/user', function() use ($app) {
  $user = $app->request->getBody();

  if (!isset($user['password'])) {
    $app->halt(400);
  }

  $user['password'] = hash('sha512', $user['password']);

  try {
    $uid = \User\saveUser($user);

    if ($uid) $app->response->setStatus(201);
    else $app->response->setStatus(400);
  } catch (PDOException $e) {
    $app->halt(500, 'Error: ' . $e->getMessage());
  }

  if ($uid) echo json_encode(array('id' => $uid));
});
