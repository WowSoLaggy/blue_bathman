<?php

require_once __DIR__.'/mysql.php';


class User
{
  public $id = -1;
  public $name = '';
  public $is_admin = false;
}


function get_users_from_db_by_inds($connection, array $user_inds) : array
{
  $inds_str = implode(',', $user_inds);

  $query = 'SELECT id, name, admin FROM users_tbl WHERE id IN ('.$inds_str.')';
  $result = mysqli_query($connection, $query);
  
  $users = array();
  $num_users = mysqli_num_rows($result);
  for ($i = 0; $i < $num_users; $i++)
  {
    $user = new User();
    $user->id = mysqli_result($result, $i, 'id');
    $user->name = mysqli_result($result, $i, 'name');
    $user->is_admin = mysqli_result($result, $i, 'admin');

    array_push($users, $user);
  }

  mysqli_free_result($result);

  return $users;
}


function get_user_from_db_by_ind($connection, int $user_ind)
{
  $query = "SELECT name, admin FROM users_tbl WHERE id=".$user_ind." LIMIT 1";
  $result = mysqli_query($connection, $query);
  $num_users = mysqli_num_rows($result);

  $user = null;

  if ($num_users > 0)
  {
    $user = new User();
    $user->id = mysqli_result($result, 0, 'id');
    $user->name = mysqli_result($result, 0, 'name');
    $user->is_admin = mysqli_result($result, 0, 'admin');
  }

  mysqli_free_result($result);

  return $user;
}

function get_user_from_db($connection, string $user_id)
{
  $user_ind = get_user_ind($connection, $user_id);
  return get_user_from_db_by_ind($connection, $user_ind);
}


function get_user(string $user_id)
{
  $connection = connect();
  $user = get_user_from_db($connection, $user_id);
  disconnect($connection);

  return $user;
}


function get_user_ind($connection, string $user_id) : int
{
  $query = 'SELECT id FROM users_tbl WHERE user_id='.$user_id.' LIMIT 1';
  $result = mysqli_query($connection, $query);
  
  if (mysqli_num_rows($result) == 0)
    throw new Exception('No user found for user_id: \''.$user_id.'\'');

  $user_ind = mysqli_result($result, 0, 'id');

  mysqli_free_result($result);

  return $user_ind;
}

?>
