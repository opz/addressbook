<?php
/**
 * Miscellaneous utility functions, mainly for PDO
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

require dirname(__FILE__) . '/config.php';

/**
 * Collection of convenience methods
 */
class Utils {

  /**
   * Connects to the configured database
   *
   * @return object the database handler
   */
  public static function connect() {
    static $dbh = null;

    return $dbh === null
      ? $dbh = new PDO('mysql:host=' . Config::HOST . ';dbname=' . Config::DBNAME,
          Config::DBUSER,
          Config::DBPASS)
      : $dbh;
  }

  /**
   * Binds values with keys listed in $params to PDO statement.
   * Any param without a corresponding value gets bound as NULL.
   *
   * @param object $sth reference to a PDO statement
   * @param array $params parameters to be bound
   * @param array $values values with matching param keys
   */
  public static function bindSetParams(&$sth, $params, $values) {
    if (!($sth || $params || $values)) return false;

    array_walk(array_replace(array_fill_keys($params, null), $values),
      function($value, $key) use ($sth, $params) {
        if (in_array($key, $params)) $sth->bindParam(":$key", $value);
      });
  }

  /**
   * Sets appropriate REST response code then outputs json data
   *
   * @param object $response the slim app response object
   * @param mixed $data the data to be output
   * @return string encoded output data on success, false on failure
   */
  public static function restResponse($response, $data) {
    if ($data === false) $response->setStatus(400);
    else {
      if (count($data) === 0) $response->setStatus(404);
      else $response->setStatus(200);
      return json_encode($data);
    }

    return false;
  }
}

?>
