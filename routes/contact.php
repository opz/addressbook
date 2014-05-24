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

$app->post('/contact/', function() use ($app) {
  $contact = json_decode($app->request->post('contact'));
  print_r($app->request);

  $first = $contact['first'];
  $last = $contact['last'];
  $email = $contact['email'];
  $address = $contact['address'];
  $phone = $contact['phone'];
  $notes = $contact['notes'];

  try {
    $dbh = connect();

    $sql = 'insert into contact (first, last, email, address, phone, notes) values (?, ?, ?, ?, ?, ?)';
    $sth = $dbh->prepare($sql);
    $sth->execute(array($first, $last, $email, $address, $phone, $notes));
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      $app->response->setStatus(500);

      die();
  }
});

?>
