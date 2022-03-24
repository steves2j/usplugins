<?php
require_once ('../../../../../users/init.php');
require_once ($abs_us_root.$us_url_root."usersc/plugins/chat/chat-app/helpers.php");
$event_override = $event_id = 1; //compatibility

$resp = ['success' => false];
$type = Input::get('type');
$msg = $user->data()->fname . ' ' . $user->data()->lname . ' ' . $user->data()->id;
$msg .= $type == 'open'? " joined the chat." : " left the chat.";
$tz = date_default_timezone_get();
$eastern = new DateTimeZone($tz);
$date = new DateTime('NOW');
$utcDate = $date->setTimeZone(new DateTimeZone('UTC'));
$formattedDate = $utcDate->format('Y-m-d H:i:s');
if(!empty($msg)) {
  $data = [
    'created_at' => $formattedDate,
    'user_id' => $user->data()->id,
    'event_id' => $event_override,
    'msg' => $msg,
    'type' => 1
  ];
  $db = DB::getInstance();
  $db->query("DELETE FROM plg_chat_sessions WHERE user_id = ? AND event_id = ?",[$user->data()->id, $event_override]);

  $result = $db->insert('plg_chat_messages',$data);

  if($type == 'open'){
    $db->insert('plg_chat_sessions',['user_id'=>$user->data()->id,'event_id'=>$event_override]);
  }
  $resp['success'] = $result;
}

jsonResponse($resp);
