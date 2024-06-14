<?php

require_once __DIR__.'/groups.php';
require_once __DIR__.'/mysql.php';
require_once __DIR__.'/users.php';


function get_user_subs_by_ind($connection, int $user_ind) : array
{
  $query = 'SELECT group_id FROM subs_tbl WHERE `user_id`='.$user_ind;
  $result = mysqli_query($connection, $query);

  $subs = array();
  $num_subs = mysqli_num_rows($result);
  for ($i = 0; $i < $num_subs; $i++)
  {
    $group_id = mysqli_result($result, $i, 'group_id');
    array_push($subs, $group_id);
  }

  mysqli_free_result($result);
  return $subs;
}


function get_user_subs_by_id($connection, string $user_id, bool $with_own) : array
{
  $user_ind = get_user_ind($connection, $user_id);
  $subs = get_user_subs_by_ind($connection, $user_ind);

  if ($with_own)
  {
    $own_group = get_user_group($connection, $user_ind);
    if ($own_group != -1)
      array_push($subs, $own_group);
  }

  return $subs;
}


function get_user_subs_test(string $user_id) : array
{
  $connection = connect();
  $subs = get_user_subs_by_id($connection, $user_id, true);
  disconnect($connection);

  return $subs;
}

//print_r(get_user_subs_test('305099932'));
//print_r(get_user_subs_test('1092343373'));


?>
