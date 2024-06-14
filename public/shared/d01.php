<?php

require_once __DIR__.'/mysql.php';


function is_d01_from_db($connection, string $user_id, string $d01) : bool
{
  if ($d01 != 'd0' && $d01 != 'd1')
    throw new Exception('Invalid input par: \''.$d01.'\'.');

  $query = 'SELECT '.$d01.' FROM users_tbl WHERE user_id='.$user_id.' LIMIT 1';
  $result = mysqli_query($connection, $query);
  $results_count = mysqli_num_rows($result);

  $is_d01 = false;
  if ($results_count > 0)
    $is_d01 = mysqli_result($result, 0, $d01);

  mysqli_free_result($result);

  return $is_d01;
}

function is_d01(string $user_id, string $d01) : bool
{
  $connection = connect();
  $is_d01 = is_d01_from_db($connection, $user_id, $d01);
  disconnect($connection);

  return $is_d01;
}


function switch_d01_in_db($connection, string $user_id, string $d01, bool $value)
{
  if ($d01 != 'd0' && $d01 != 'd1')
    throw new Exception('Invalid input par: \''.$d01.'\' for user \''.$user_id.'\'.');

  $mysql_value = $value ? 'TRUE' : 'FALSE';
  $query = 'UPDATE users_tbl SET '.$d01.'='.$mysql_value.' WHERE user_id='.$user_id.' LIMIT 1';
  $result = mysqli_query($connection, $query);
  if (!$result)
    throw new Exception('Failed to update \''.$d01.'\' for user \''.$user_id.'\'.');
}

function switch_d01(string $user_id, string $d01, bool $value)
{
  $connection = connect();
  switch_d01_in_db($connection, $user_id, $d01, $value);
  disconnect($connection);
}

?>
