<?php

require_once __DIR__.'/mysql.php';
require_once __DIR__.'/subs.php';
require_once __DIR__.'/users.php';


class Calendar
{
  public $id = -1;
  public $name = '';
  public $owner = -1;
  public $owner_name = '';
  public $public = false;
  public $dates_count = 0;
}


function get_all_shared_cals($connection, string $user_id) : array
{
  $query = 'SELECT id, name, owner, public FROM groups_tbl WHERE NOT `personal`';
  $result = mysqli_query($connection, $query);
  
  $cals = array();
  $num_groups = mysqli_num_rows($result);
  for ($i = 0; $i < $num_groups; $i++)
  {
    $cal = new Calendar();

    $cal->id = mysqli_result($result, $i, 'id');
    $cal->name = mysqli_result($result, $i, 'name');
    $cal->owner = mysqli_result($result, $i, 'owner');
    $cal->public = mysqli_result($result, $i, 'public');

    array_push($cals, $cal);
  }

  mysqli_free_result($result);
  return $cals;
}


function get_dates_count_in_cal($connection, int $cal_id) : int
{
  $query = 'SELECT COUNT(*) AS CNT FROM bdays_tbl WHERE `group`='.$cal_id;
  $result = mysqli_query($connection, $query);

  $num_rows = mysqli_num_rows($result);
  if ($num_rows != 1)
    throw new Exception('Incorrect num rows for: \''.$query.'\'');
  $count = mysqli_result($result, 0, 'CNT');

  mysqli_free_result($result);

  return $count;
}


function get_available_cals_for_user_formatted(string $user_id) : string
{
  $connection = connect();
  
  $cals = get_all_shared_cals($connection, $user_id);
  $user_ind = get_user_ind($connection, $user_id);
  $subs = get_user_subs_by_id($connection, $user_id, false);
  
  // Get array of owners
  $owners_list = array();
  foreach ($cals as &$cal)
  {
    $cal->dates_count = get_dates_count_in_cal($connection, $cal->id);
    array_push($owners_list, $cal->owner);
  }

  $users = get_users_from_db_by_inds($connection, $owners_list);

  disconnect($connection);


  // Create map of owners
  $owners_map = array();
  foreach ($users as &$user)
    $owners_map[$user->id] = $user->name;

  // Filter cals by groups and assign owners

  $cals_own = array();
  $cals_sub = array();
  $cals_other = array();
  foreach ($cals as &$cal)
  {
    $cal->owner_name = $owners_map[$cal->owner];

    if ($cal->owner == $user_ind)
      array_push($cals_own, $cal);
    else if (in_array($cal->id, $subs))
      array_push($cals_sub, $cal);
    else if ($cal->public)
      array_push($cals_other, $cal);
  }


  function append_cals(string &$text, array &$cals, bool $with_owner)
  {
    foreach ($cals as &$cal)
    {
      $text .= '"'.$cal->name.'" (событий: '.$cal->dates_count;
      if ($with_owner)
        $text .= ', админ: '.$cal->owner_name;
      $text .= ')'.chr(10);
    }
  }


  $out = '';

  // Own calendars

  if (!empty($cals_own))
    $out .= 'Твои календари:'.chr(10);
  append_cals($out, $cals_own, false);

  // Subscribed calendars

  if (!empty($cals_sub))
  {
    if (!empty($out))
      $out .= chr(10);
    $out .= 'Календари, на которые ты подписан:'.chr(10);
  }
  append_cals($out, $cals_sub, true);


  // Other public calendars

  if (!empty($cals_other))
  {
    if (!empty($out))
      $out .= chr(10);
    $out .= 'Другие открытые календари:'.chr(10);
  }
  append_cals($out, $cals_other, true);

  return $out;
}

//echo get_available_cals_for_user_formatted('305099932');


?>
