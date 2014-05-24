<?php
/**
 * REST API routes for contacts
 *
 * @author Will Shahda <will.shahda@gmail.com
 */

$app->get('/contact/:id', function($id) use ($app) {
  if ($id) {
    try {
      $dbh = connect();

      $sql = 'select * from contacts where id = ?';
      $sth = $dbh->prepare($sql);
      $sth->execute(array($id));

      $contact = $sth->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      $app->response->setStatus(500);

      die();
    }

    $app->response->setStatus(200);

    echo json_encode($contact);
  }
});

?>
