<?php

require_once __DIR__.'/mysql.php';
require_once __DIR__.'/subs.php';
require_once __DIR__.'/translate.php';


class BDay
{
  public $id = 0;
  public $name = "";
  public $date = '2000-1-1';
  public $bday = '1-1';
}


//echo get_bdays_formatted('305099932', 12);


function get_bdays_from_db($connection, string $user_id)
{
  $subs = get_user_subs_by_id($connection, $user_id, true);
  $subs_str = implode(',', $subs);

  $query = 'SELECT id, name, date FROM bdays_tbl WHERE NOT `fake` AND `group` IN ('.$subs_str.')';
  $result = mysqli_query($connection, $query);
  
  $bdays = array();
  $num_bdays = mysqli_num_rows($result);
  for ($i = 0; $i < $num_bdays; $i++)
  {
    $bday = new BDay();

    $bday->id = mysqli_result($result, $i, 'id');
    $bday->name = mysqli_result($result, $i, 'name');
    $bday->date = mysqli_result($result, $i, 'date');
    $bday->bday = date('2020-m-d', strtotime($bday->date));

    array_push($bdays, $bday);
  }

  mysqli_free_result($result);

  return $bdays;
}


function bdays_sorter($user1, $user2)
{
  return strtotime($user1->bday) - strtotime($user2->bday);
}


function get_bdays_sorted(string $user_id)
{
  $connection = connect();
  $bdays = get_bdays_from_db($connection, $user_id);
  disconnect($connection);

  usort($bdays, "bdays_sorter");

  // Move bdays with passed bday to the end of list
  for ($i = 0; $i < count($bdays); $i++)
  {
    if (strtotime($bdays[0]->bday) < strtotime(date('2020-m-1')))
      array_push($bdays, array_shift($bdays));
  }

  return $bdays;
}


function get_bdays_formatted(string $user_id, int $months_to_show)
{
  $bdays = get_bdays_sorted($user_id);

  $out = "";
  $cur_month = "";
  $months_shown = 0;

  foreach ($bdays as &$bday)
  {
    $bday_month = date('M', strtotime($bday->date));
    if ($cur_month != $bday_month)
    {
      ++$months_shown;
      if ($months_shown > $months_to_show)
        break;

      if (!empty($cur_month))
        $out .= chr(10);
      
      $out .= translate_month_en2ru($bday_month).chr(10);
      $out .= "-----------------------------------".chr(10);
    }

    $date_formatted = date('d M', strtotime($bday->date));
    $date_ru = translate_month_en2ru($date_formatted);
    $date_birth = new DateTime($bday->date);
    $date_now = new DateTime(date('d.m.Y', strtotime("-1 days")));
    $date_diff = $date_now->diff($date_birth);
    $years_full = $date_diff->y;
    if (strtotime($bday->bday) >= strtotime(date('2020-m-d')) ||
      date('m') != date('m', strtotime($bday->bday)))
    {
      ++$years_full;
    }

    $out .= $date_ru.' - '.$bday->name.' ('.$years_full.' yo.)'.chr(10);

    $cur_month = $bday_month;
  }

  return $out;
}

function validate_bday_pars(string $name, string $date) : string
{
  if (!preg_match('/^([а-яА-ЯЁёa-zA-Z0-9 ]+)$/u', $name))
    return 'Имя должно содержать только буквы и цифры (А-Я, а-я, A-Z, a-z, 0-9)!'.chr(10).'Попробуй ещё раз, это не сложно';
  
  $tokens = explode('-', $date);
  if (count($tokens) != 3)
    return 'Дата должна быть обязательно в формате yyyy-mm-dd, например 13 ноября 1988 года надо записать так:'.chr(10).'1988-11-13'.chr(10).'Попробуй ещё раз, это не сложно';

  $year_str = $tokens[0];
  $month_str = $tokens[1];
  $day_str = $tokens[2];

  if (!ctype_digit($year_str) || !ctype_digit($month_str) || !ctype_digit($day_str))
    return 'Год, месяц и день рождения могут содержать только цифры! Например 1988-11-13. Попробуй ещё раз, это не сложно';

  $year = intval($year_str);
  $month = intval($month_str);
  $day = intval($day_str);

  if ($month < 1 || 12 < $month)
    return 'Месяц может быть от 01 (январь) до 12 (декабрь). Попробуй ещё раз, у тебя получится';
  
  if ($day < 1)
    return 'Число не бывает меньше 1. Попробуй ещё раз, я в тебя верю';

  $max_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
  if ($day > $max_days)
    return 'Указанный тобой месяц в '.$year.' году содержит всего '.$max_days.' дней! Уточни пожалуйста число...';

  if (strtotime($date) > strtotime('now'))
    return 'День рождения не может быть в будущем! Попробуй ещё раз';

  return '';
}

function add_bday_to_db($connection, string $name, string $date, int $group) : string
{
  $query = 'INSERT INTO bdays_tbl VALUES (NULL, \''.$name.'\', \''.$date.'\', 0, '.$group.')';
  $result = mysqli_query($connection, $query);
  if (!$result)
    return 'Ой, вроде всё правильно, но что-то не получилось. Напиши Антону - он поможет разобраться';

  return '';
}

function del_bday_from_db($connection, string $name, string $date, int $group) : string
{
  $query = 'DELETE FROM bdays_tbl WHERE `name`=\''.$name.'\' AND `date`=\''.$date.'\' AND `group`='.$group;
  $result = mysqli_query($connection, $query);
  if (!$result)
    return 'Ой, вроде всё правильно, но что-то не получилось. Напиши Антону - он поможет разобраться';

  return '';
}

// Returns true if such a bday already exists
function check_exists_in_db($connection, string $name, string $date, int $group) : bool
{
  $query = 'SELECT COUNT(*) as CNT FROM bdays_tbl WHERE `name`=\''.$name.'\' AND `date`=\''.$date.'\' AND `group`='.$group;
  $result = mysqli_query($connection, $query);

  $num_rows = mysqli_num_rows($result);
  if ($num_rows != 1)
    throw new Exception('Incorrect num rows for: \''.$query.'\'');
  $count = mysqli_result($result, 0, 'CNT');

  mysqli_free_result($result);

  return $count > 0;
}

// Returns an error message or empty string if successfully added
function add_bday(string $name, string $date, int $group) : string
{
  $validation_result = validate_bday_pars($name, $date);
  if (!empty($validation_result))
    return $validation_result;

  $connection = connect();
  if (check_exists_in_db($connection, $name, $date, $group))
    $result = 'Ой, а такой день рождения уже есть! Проверь сам!';
  else
    $result = add_bday_to_db($connection, $name, $date, $group);
  disconnect($connection);

  return $result;
}

// Returns an error message or empty string if successfully deleted
function del_bday(string $name, string $date, int $group) : string
{
  $validation_result = validate_bday_pars($name, $date);
  if (!empty($validation_result))
    return $validation_result;

  $connection = connect();
  if (!check_exists_in_db($connection, $name, $date, $group))
    $result = 'Что-то я не могу найти такой день рождения... Перепроверь имя и дату - они должны точно совпадать';
  else
    $result = del_bday_from_db($connection, $name, $date, $group);
  disconnect($connection);

  return $result;
}

?>
