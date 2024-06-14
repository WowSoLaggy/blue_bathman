<?php

require_once __DIR__.'/bot_commands.php';
require_once __DIR__.'/bot_conf.php';

require_once __DIR__.'/../shared/bdays.php';
require_once __DIR__.'/../shared/commands.php';
require_once __DIR__.'/../shared/d01.php';
require_once __DIR__.'/../shared/keyboard.php';
require_once __DIR__.'/../shared/logger.php';
require_once __DIR__.'/../shared/users.php';


function process(string $user_id, string $chat_id, string $text = null) : bool
{
  $auth_error_msg = 'Sorry, you are not authorized';

  $user = get_user($user_id);
  if (is_null($user))
  {
    send_message($auth_error_msg, $chat_id);
    return false;
  }

  $tokens = explode(' ', $text);
  if (empty($tokens))
    return true;

  $input_cmd = $tokens[0];

  $cmds = get_commands();
  foreach ($cmds as &$cmd)
  {
    if (($input_cmd !== $cmd->command) && !empty($cmd->command))
      continue;
    
    $granted = $user->is_admin || !$cmd->admin;
    if (!$granted)
    {
      send_message($auth_error_msg, $chat_id);
      return false;
    }

    $ctx = new CommandCtx($user_id, $chat_id, $tokens);
    $func = $cmd->func;
    $func($ctx);
    break;
  }

  return true;
}


function process_callback(array $callback)
{
  $user_id = $callback['from']['id'];
  $chat_id = $callback['message']['chat']['id'];
  $text = isset($callback['data']) ? $callback['data'] : null;

  $is_auth = process($user_id, $chat_id, $text);

  log_callback($callback, $is_auth);
}


function process_message(array $message)
{
  $user_id = $message['from']['id'];
  $chat_id = $message['chat']['id'];
  $text = isset($message['text']) ? $message['text'] : null;

  $is_auth = process($user_id, $chat_id, $text);

  log_message($message, $is_auth);
}


//
// MAIN
//


$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update)
{
  // receive wrong update, must not happen
  exit;
}

if (isset($update['callback_query']))
  process_callback($update['callback_query']);
else if (isset($update['message']))
  process_message($update['message']);


?>
