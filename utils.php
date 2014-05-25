<?php
/**
 * Miscellaneous utility functions, mainly for PDO
 *
 * @author Will Shahda <will.shahda@gmail.com
 */

require dirname(__FILE__) . '/config.php';

function connect() {
  global $host, $dbname, $dbuser, $dbpass;

  static $dbh = null;

  return $dbh === null
    ? $dbh = new PDO('mysql:host=' . $host . ';dbname=' . $dbname,
        $dbuser,
        $dbpass)
    : $dbh;
}

/**
 * Binds values with keys listed in $params to PDO statement.
 * Any param without a corresponding value gets bound as NULL.
 *
 * @param $sth object reference to a PDO statement
 * @param $params array parameters to be bound
 * @param $values array values with matching param keys
 */
function bindSetParams(&$sth, $params, $values) {
  array_walk(array_replace(array_fill_keys($params, null), $values),
    function($value, $key) use ($sth) {
      $sth->bindParam(":$key", $value);
    });
}

?>
