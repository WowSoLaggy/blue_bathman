<?php


require_once __DIR__.'/mysql.php';
require_once __DIR__.'/users.php';


function create_new_group($connection, int $owner, bool $personal, bool $public)
{
  $personal_mysql = $personal ? 'TRUE' : 'FALSE';
  $public_mysql = $public ? 'TRUE' : 'FALSE';

  $query = 'INSERT INTO groups_tbl VALUES (NULL, '.$owner.', '.$personal_mysql.', '.$public_mysql.')';
  $result = mysqli_query($connection, $query);
  if (!$result)
    throw new Exception('Failed to create a group by query: "'.$query.'".');
}

function get_user_group($connection, string $user_ind) : int
{
  $query = 'SELECT id FROM groups_tbl WHERE owner='.$user_ind.' AND personal=TRUE LIMIT 1';
  $result = mysqli_query($connection, $query);
  
  if (mysqli_num_rows($result) == 0)
    $user_group = -1;
  else
    $user_group = mysqli_result($result, 0, 'id');

  mysqli_free_result($result);

  return $user_group;
}

function create_user_group($connection, string $user_ind) : int
{
  create_new_group($connection, $user_ind, true, false);
  $user_group = get_user_group($connection, $user_ind);
  return $user_group;
}


function get_or_create_group(string $user_id) : int
{
  $connection = connect();

  $user_ind = get_user_ind($connection, $user_id);
  $user_group = get_user_group($connection, $user_ind);
  
  if ($user_group == -1)
    $user_group = create_user_group($connection, $user_ind);
  
  disconnect($connection);
  return $user_group;
}


?>
