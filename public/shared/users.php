<?php

require_once __DIR__.'/mysql.php';


class User
{
  public $id = -1;
  public $user_id = -1;
  public $name = '';
  public $is_admin = false;
  public $d0 = false;
  public $d1 = false;
}


function get_users_from_db_by_inds($connection, array $user_inds) : array
{
  $inds_str = implode(',', $user_inds);

  $query = 'SELECT * FROM users_tbl WHERE id IN ('.$inds_str.')';
  $result = mysqli_query($connection, $query);
  
  $users = array();
  $num_users = mysqli_num_rows($result);
  for ($i = 0; $i < $num_users; $i++)
  {
    $user = new User();
    $user->id = mysqli_result($result, $i, 'id');
    $user->user_id = mysqli_result($result, $i, 'user_id');
    $user->name = mysqli_result($result, $i, 'name');
    $user->is_admin = mysqli_result($result, $i, 'admin');
    $user->d0 = mysqli_result($result, $i, 'd0');
    $user->d1 = mysqli_result($result, $i, 'd1');

    array_push($users, $user);
  }

  mysqli_free_result($result);

  return $users;
}


function get_user_from_db_by_ind($connection, int $user_ind)
{
  $query = "SELECT * FROM users_tbl WHERE id=".$user_ind." LIMIT 1";
  $result = mysqli_query($connection, $query);
  $num_users = mysqli_num_rows($result);

  $user = null;

  if ($num_users > 0)
  {
    $user = new User();
    $user->id = mysqli_result($result, 0, 'id');
    $user->user_id = mysqli_result($result, 0, 'user_id');
    $user->name = mysqli_result($result, 0, 'name');
    $user->is_admin = mysqli_result($result, 0, 'admin');
    $user->d0 = mysqli_result($result, 0, 'd0');
    $user->d1 = mysqli_result($result, 0, 'd1');
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


function get_all_users_from_db($connection) : array
{
  $query = "SELECT * FROM users_tbl";
  $result = mysqli_query($connection, $query);
  $num_users = mysqli_num_rows($result);

  $users = array();

  for ($i = 0; $i < $num_users; $i++)
  {
    $user = new User();
    $user->id = mysqli_result($result, 0, 'id');
    $user->user_id = mysqli_result($result, $i, 'user_id');
    $user->name = mysqli_result($result, 0, 'name');
    $user->is_admin = mysqli_result($result, 0, 'admin');
    $user->d0 = mysqli_result($result, $i, 'd0');
    $user->d1 = mysqli_result($result, $i, 'd1');
    array_push($users, $user);
  }

  mysqli_free_result($result);

  return $users;
}


function get_all_users() : array
{
  $connection = connect();
  $users = get_all_users_from_db($connection);
  disconnect($connection);

  return $users;
}

?>
