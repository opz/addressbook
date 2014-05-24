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

?>
