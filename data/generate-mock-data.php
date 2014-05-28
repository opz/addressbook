<?php

function csv_to_array($filename='', $delimiter=',', $enclosure='"')
{
  if(!file_exists($filename) || !is_readable($filename))
    return false;

  $header = null;
  $data = array();
  if (($handle = fopen($filename, 'r')) !== false)
  {
    while (($line = fgets($handle)) !== false) {
      $row = explode(',', $line);

      if(!$header) $header = $row;
      else $data[] = array_combine($header, $row);
    }
    fclose($handle);
  }
  return $data;
}

$csv = csv_to_array($argv[1], ',', '');
$uid = $argv[2];

echo 'insert into contacts (uid, first, last, email, address, phone, notes) values ';

for ($i = 0; $i < count($csv); $i++) {
  foreach (array(
    'GivenName' => 'first',
    'Surname' => 'last',
    'StreetAddress' => 'street',
    'City' => 'city',
    'State' => 'state',
    'ZipCode' => 'zip',
    'EmailAddress' => 'email',
    'TelephoneNumber' => 'phone',
    'Occupation' => 'occupation',
    'Company' => 'company',
    'Vehicle' => 'vehicle'
  ) as $key => $var) {
    $$var = addslashes($csv[$i][$key]);
  }

  $notes = array();
  switch (rand(1, 3)) {
    case 1:
      $notes[] = "Drives a $vehicle.";
      if (rand(1, 2) === 1) break;
    case 2:
      $notes[] = "Works at $company.";
      if (rand(1, 2) === 1) break;
    case 3:
      $notes[] = "Works as a $occupation.";
      if (rand(1, 2) === 1) break;
  }

  $notes = implode(' ', $notes);
  echo "($uid, '$first', '$last', '$email', '$street, $city $state $zip', '$phone', '$notes')";

  if ($i !== count($csv) - 1) echo ",\n";
}

echo ';';

?>
