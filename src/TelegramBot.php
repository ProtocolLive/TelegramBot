<?php
//2021.09.11.02
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBot

require(__DIR__ . '/basics.php');
require(__DIR__ . '/factorys.php');

class TelegramBot extends TelegramBot_Basics{
  private array $Me;
  private object $Server;
  private string $Url = 'https://api.telegram.org/bot';
  private string $UrlFiles= 'https://api.telegram.org/file/bot';
  private int $Debug = self::DebugNone;
  private string $DirLogs;

  public const DebugAll = -1;
  public const DebugNone = 0;
  public const DebugWebhook = 1;
  public const DebugSend = 2;

  private function ParseServer(array $Server){
    if(($Server['message']['entities'][0]['type'] ?? null) === 'bot_command'
    and ($Server['message']['entities'][0]['offset'] ?? null) === 0):
      $this->Server->Event = new TelegramBot_FactoryEventCommand;
      $this->ParseUser($Server['message']);
      $this->ParseChat($Server['message']);
      $this->ParseCommand($Server['message']['text']);
    elseif(isset($Server['message']['text'])):
      $this->Server->Event = new TelegramBot_FactoryEventText;
      $this->Server->Event->Reply = $Server['reply_to_message']['message_id'] ?? null;
      $this->Server->Event->Msg = $Server['message']['text'];
      $this->Server->Event->Id = $Server['message']['message_id'];
      $this->ParseUser($Server['message']);
      $this->ParseChat($Server['message']);
    elseif(isset($Server['message']['document'])):
      $this->Server->Event = new TelegramBot_FactoryEventDocument;
      $this->Server->Event->Id = $Server['message']['message_id'];
      $this->Server->Event->File = $Server['message']['document']['file_id'];
      $this->Server->Event->Name = $Server['message']['document']['file_name'];
      $this->ParseUser($Server['message']);
      $this->ParseChat($Server['message']);
    elseif(isset($Server['message']['photo'])):
      $this->Server->Event = new TelegramBot_FactoryEventImage;
      $this->Server->Event->Id = $Server['message']['message_id'];
      $this->Server->Event->Miniature = $Server['message']['photo'][0]['file_id'];
      $this->Server->Event->File = $Server['message']['photo'][1]['file_id'];
      $this->ParseUser($Server['message']);
      $this->ParseChat($Server['message']);
    elseif(isset($Server['message']['voice'])):
      $this->Server->Event = new TelegramBot_FactoryEventVoice;
      $this->Server->Event->Id = $Server['message']['message_id'];
      $this->Server->Event->File = $Server['message']['voice']['file_id'];
      $this->ParseUser($Server['message']);
      $this->ParseChat($Server['message']);
    elseif(isset($Server['message']['new_chat_participant'])):
      $this->Server->Event = new TelegramBot_FactoryEventGroupUpdate;
      $this->Server->Event->Action = self::GroupUpdate_Add;
      $this->ParseUser($Server['message']);
      $this->ParseChat($Server['message']);
    elseif(isset($Server['message']['left_chat_participant'])):
      $this->Server->Event = new TelegramBot_FactoryEventGroupUpdate;
      $this->Server->Event->Action = self::GroupUpdate_Quit;
      $this->ParseUser($Server['message']);
      $this->ParseChat($Server['message']);
    elseif(isset($Server['message']['message_auto_delete_timer_changed'])):
      $this->Server->Event = new TelegramBot_FactoryEventGroupMe;
      $this->Server->Event->Action = TelegramBot::GroupMe_AutoClean;
      $this->ParseUser($Server['message']);
      $this->ParseChat($Server['message']);
    elseif(isset($Server['message']['dice'])):
      $this->Server->Event = new TelegramBot_FactoryEventDice;
      $this->Server->Event->Emoji = $Server['message']['dice']['emoji'];
      $this->Server->Event->Value = $Server['message']['dice']['value'];
      $this->ParseUser($Server['message']);
      $this->ParseChat($Server['message']);
    elseif(isset($Server['my_chat_member'])):
      $this->Server->Event = new TelegramBot_FactoryEventGroupMe;
      if($Server['my_chat_member']['new_chat_member']['status'] === 'member'):
        $this->Server->Event->Type->Action = self::GroupMe_Add;
      elseif($Server['my_chat_member']['new_chat_member']['status'] === 'left'):
        $this->Server->Event->Type->Action = self::GroupMe_Quit;
      elseif($Server['my_chat_member']['new_chat_member']['status'] === 'administrator'):
        $this->Server->Event->Type->Action = self::GroupMe_Admin;
      elseif($Server['my_chat_member']['new_chat_member']['status'] === 'kicked'):
        $this->Server->Event->Type->Action = self::GroupMe_Kicked;
      endif;
      $this->ParseUser($Server['my_chat_member']);
      $this->ParseChat($Server['my_chat_member']);
    elseif(isset($Server['callback_query'])):
      $this->Server->Event = new TelegramBot_FactoryEventCallback;
      $this->Server->Event->Id = $Server['callback_query']['message']['message_id'];
      $temp = explode(' ', $Server['callback_query']['data']);
      $this->Server->Event->Data = $temp[0];
      if(isset($temp[1])):
        parse_str($temp[1], $this->Server->Event->Parameters);
      endif;
      $this->ParseUser($Server['callback_query']);
      $this->ParseChat($Server['callback_query']['message']);
    elseif(isset($Server['inline_query'])):
      $this->Server->Event = new TelegramBot_FactoryEventInline;
      $this->Server->Event->Id = $Server['inline_query']['id'];
      $this->Server->Event->Parameter = $Server['inline_query']['query'];
      $this->Server->Event->ChatType = $Server['inline_query']['chat_type'];
      $this->ParseUser($Server['inline_query']);
    endif;
  }

  private function ParseUser(array $Server):void{
    $this->Server->Event->User->Id = $Server['from']['id'];
    $this->Server->Event->User->Bot = $Server['from']['is_bot'];
    $this->Server->Event->User->Name = $Server['from']['first_name'];
    $this->Server->Event->User->NameLast = $Server['from']['last_name'] ?? null;
    $this->Server->Event->User->Nick = $Server['from']['username'] ?? null;
    $this->Server->Event->User->Language = $Server['from']['language_code'] ?? null;
  }

  private function ParseChat(array $Server):void{
    $this->Server->Event->Chat->Id = $Server['chat']['id'];
    if($Server['chat']['type'] === 'private'):
      $this->Server->Event->Chat->Type = self::Chat_Private;
    elseif($Server['chat']['type'] === 'group'):
      $this->Server->Event->Chat->Type = self::Chat_Group;
      $this->Server->Event->Chat->Name = $Server['chat']['title'];
    endif;
  }

  private function ParseCommand(string $Msg):void{
    $me = '@' . $this->Me['username'];
    $len = strlen($me);
    $temp = explode(' ', $Msg);
    if(substr($temp[0], -$len) === $me):
      $Msg = substr($Msg, 0, -$len);
    endif;
    $pos = strpos($Msg, ' ');
    if($pos === false):
      $this->Server->Event->Command = substr($Msg, 1);
    else:
      $this->Server->Event->Command = substr($Msg, 1, $pos - 1);
      $this->Server->Event->Parameters = substr($Msg, $pos + 1);
    endif;
  }

  /**
   * @return array|object|true|null
   */
  private function ServerGet(string $Msg, bool $ReturnArray = false, bool $Async = false){
    $temp = $this->Url . $Msg;
    if(($this->Debug & self::DebugSend) === self::DebugSend):
      $this->DebugLog($this->DirLogs . '/send.log', $temp);
    endif;
    $curl = curl_init($temp);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Protocol SimpleTelegramBot');
    curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . '/cacert.pem');
    if($Async):
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 500);
      curl_setopt($curl, CURLOPT_TIMEOUT_MS, 500);
    endif;
    $temp = curl_exec($curl);
    if($temp === false):
      $this->DebugLog($this->DirLogs . '/debug.log', 'cURL error #' . curl_errno($curl) . ' ' . curl_error($curl));
      $this->Error = self::Error_CurlError;
      return null;
    endif;
    $temp = json_decode($temp, $ReturnArray);
    if(($this->Debug & self::DebugSend) === self::DebugSend):
      $this->DebugLog($this->DirLogs . '/send.log', json_encode($temp, JSON_PRETTY_PRINT));
    endif;
    if($ReturnArray):
      if($temp['ok'] === false):
        $this->Error = self::Error_Custom;
        $this->Errors[self::Error_Custom] = $temp['description'];
        return null;
      else:
        return $temp['result'];
      endif;
    else:
      if($temp->ok === false):
        $this->Error = self::Error_Custom;
        $this->Errors[self::Error_Custom] = $temp->description;
        return null;
      else:
        return $temp->result;
      endif;
    endif;
  }

  public function __construct(string $Token, string $DirLogs = __DIR__ . '/logs', int $Debug = self::DebugNone){
    if(extension_loaded('openssl') === false):
      trigger_error($this->Errors[self::Error_NoSsl], E_USER_ERROR);
    elseif(extension_loaded('curl') === false):
      trigger_error($this->Errors[self::Error_NoCurl], E_USER_ERROR);
    elseif($Token === ''):
      trigger_error($this->Errors[self::Error_NoToken], E_USER_ERROR);
    endif;
    $this->Url .= $Token;
    $this->UrlFiles .= $Token;
    $this->Debug = $Debug;
    $this->DirLogs = $DirLogs;
    if(is_file(__DIR__ . '/db.json')):
      $this->Me = json_decode(file_get_contents(__DIR__ . '/db.json'), true);
    else:
      $this->Me = $this->ServerGet('/getMe');
      file_put_contents(__DIR__ . '/db.json', json_encode($this->Me));
    endif;
    $this->Server = new TelegramBot_FactoryServer();
  }

// ------------------------ Get / Set -----------------------------

  public function CmdGet():?array{
    return $this->ServerGet('/getMyCommands');
  }

  public function CmdSet(array $Cmds){
    return $this->ServerGet('/setMyCommands?commands=' . urlencode(json_encode($Cmds)));
  }

  public function Name():string{
    return $this->Me['first_name'];
  }

  public function Id():int{
    return $this->Me['id'];
  }

  public function Nick():string{
    return $this->Me['username'];
  }

  public function JoinGroups():bool{
    return $this->Me['can_join_groups'];
  }

  public function ReadMsg():bool{
    return $this->Me['can_read_all_group_messages'];
  }

  public function InLine():bool{
    return $this->Me['supports_inline_queries'];
  }

  public function UserId():int{
    return $this->Server->Event->User->Id;
  }

  public function UserBot():bool{
    return $this->Server->Event->User->Bot;
  }

  public function UserName():string{
    return $this->Server->Event->User->Name;
  }

  public function UserNameLast():?string{
    return $this->Server->Event->User->NameLast;
  }

  public function UserNameUser():?string{
    return $this->Server->Event->User->NameUser;
  }

  public function UserLanguage():?string{
    return $this->Server->Event->User->Language;
  }

  public function Msg():string{
    return $this->Server->Event->Msg;
  }

  public function ChatId():int{
    return $this->Server->Event->Chat->Id;
  }

  public function ChatName():string{
    return $this->Server->Event->Chat->Name;
  }

  public function Event():?int{
    if(isset($this->Server->Event)):
      return $this->Server->Event->Type;
    else:
      $this->Error = self::Error_NoEvent;
      return null;
    endif;
  }

  public function MsgId():?int{
    if($this->Server->Event->Id > 0):
      return $this->Server->Event->Id;
    else:
      $this->Error = self::Error_NoEventMsg;
      return null;
    endif;
  }

  public function RepliedMsgId():?int{
    if($this->Server->Event->Reply !== null):
      return $this->Server->Event->Reply;
    else:
      $this->Error = self::Error_NoRepliedMsg;
      return null;
    endif;
  }

  public function File():?string{
    if(isset($this->Server->Event->File)):
      return $this->Server->Event->File;
    else:
      $this->Error = self::Error_NoFile;
      return null;
    endif;
  }

  public function Miniature():?string{
    if($this->Server->Event->Type === self::Event_Image):
      return $this->Server->Event->Miniature;
    else:
      $this->Error = self::Error_NoEventImage;
      return null;
    endif;
  }

  public function FileName():?string{
    if($this->Server->Event->Type === self::Event_Document):
      return $this->Server->Event->Name;
    else:
      $this->Error = self::Error_NoEventDocument;
      return null;
    endif;
  }

  public function CallbackData():?string{
    if($this->Server->Event->Type === self::Event_Callback):
      return $this->Server->Event->Data;
    else:
      $this->Error = self::Error_NoEventCallback;
      return null;
    endif;
  }

  public function Command():?string{
    return $this->Server->Event->Command;
  }

  /**
   * @return string|array|null
   */
  public function Parameters(){
    return $this->Server->Event->Parameters;
  }

//--------------------------------------------------------------------------------------

  public function WebhookSet():?bool{
    $return = $this->ServerGet('/setWebhook?url=' . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']));
    if($return === false):
      return false;
    else:
      return $this->ServerGet('/getWebhookInfo');
    endif;
  }

  public function WebhookDel():?bool{
    return $this->ServerGet('/deleteWebhook');
  }

  public function WebhookGet():?bool{
    $Server = file_get_contents('php://input');
    if($Server === ''):
      $this->Error = self::Error_NoEvent;
      return null;
    endif;
    $Server = json_decode($Server, true);
    if(($this->Debug & self::DebugWebhook) === self::DebugWebhook):
      $this->DebugLog($this->DirLogs . '/webhook.log', json_encode($Server, JSON_PRETTY_PRINT));
    endif;
    $this->ParseServer($Server);
    return true;
  }

  public function UpdatesGet(int $Start = 0, int $Limit = 100):?int{
    $Server = $this->ServerGet('/getUpdates?offset=' . $Start . '&limit=' . $Limit, true);
    if($Server === null):
      return null;
    else:
      foreach($Server as $update):
        $this->ParseServer($update);
        $last = $update['update_id'];
      endforeach;
      return $last ?? null;
    endif;
  }

  /**
   * @param string $Destination Dir, name and extension
   */
  public function DownloadImage(string $FileId, string $Destination):?bool{
    if($this->Server->Event->Type === self::Event_Image):
      $file = $this->ServerGet('/getFile?file_id=' . $FileId);
      if($file === false):
        return null;
      endif;
      $content = file_get_contents($this->UrlFiles . '/' . $file->file_path);
      file_put_contents($Destination, $content);
      return true;
    else:
      $this->Error = self::Error_NoEventImage;
      return null;
    endif;
  }

  /**
   * @param string $Destination Only the dir
   */
  public function DownloadDocument(string $FileId, string $Destination):?bool{
    if($this->Server->Event->Type === self::Event_Document):
      $file = $this->ServerGet('/getFile?file_id=' . $FileId);
      if($file === false):
        return null;
      endif;
      $content = file_get_contents($this->UrlFiles . '/' . $file->file_path);
      file_put_contents($Destination . '/' . $this->Server->Event->Name, $content);
      return true;
    else:
      $this->Error = self::Error_NoEventDocument;
      return null;
    endif;
  }

//-------------------------------------------------------------------------------------

  public function ChatGet(int $Chat):?object{
    return $this->ServerGet('/getChat?chat_id=' . $Chat);
  }

  public function Send(int $Chat, string $Msg, int $Reply = null, array $Markup = null, bool $Async = false):?object{
    if($Msg === ''):
      $this->Error = self::Error_SendNoMsg;
      return null;
    elseif(strlen($Msg) > self::MsgSizeLimit):
      $this->Error = self::Error_SendMsgTooBig;
      return null;
    endif;
    $temp = '/sendMessage?chat_id=' . $Chat . '&text=' . urlencode($Msg) . '&parse_mode=HTML';
    if($Reply !== null):
      $temp .= '&reply_to_message_id=' . $Reply;
    endif;
    if($Markup !== null):
      $temp .= '&reply_markup=' . urlencode(json_encode($Markup));
    endif;
    return $this->ServerGet($temp, false, $Async);
  }

  public function SendVoice(int $Chat, string $File):?object{
    return $this->ServerGet('/sendVoice?chat_id=' . $Chat . '&voice=' . $File);
  }

  /**
   * @param string $Photo File, FileId or URL
   */
  public function SendPhoto(int $Chat, string $Photo):?object{
    if(file_exists($Photo)):
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $this->Url . '/sendPhoto');
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, [
        'chat_id' => $Chat,
        'photo' => new CurlFile($Photo)
      ]);
      curl_setopt($curl, CURLOPT_INFILESIZE, filesize($Photo));
      $temp = curl_exec($curl);
      $temp = json_decode($temp);
      if(($this->Debug & self::DebugSend) === self::DebugSend):
        $this->DebugLog($this->DirLogs . '/send.log', $this->Url . '/sendPhoto');
        $this->DebugLog($this->DirLogs . '/send.log', json_encode($temp, JSON_PRETTY_PRINT));
      endif;
      if($temp->ok === false):
        $this->Errors[0] = $temp->description;
        return null;
      else:
        return $temp->result;
      endif;
    else:
      return $this->ServerGet('/sendPhoto?chat_id=' . $Chat . '&photo=' . $Photo);
    endif;
  }

  public function Forward(int $To, int $From, int $Msg):?object{
    return $this->ServerGet('/forwardMessage?chat_id=' . $To . '&from_chat_id=' . $From . '&message_id=' . $Msg);
  }

  public function Copy(int $To, int $From, int $Msg):?object{
    return $this->ServerGet('/copyMessage?chat_id=' . $To . '&from_chat_id=' . $From . '&message_id=' . $Msg);
  }

  public function SendAction(int $Chat, string $Status):?bool{
    return $this->ServerGet('/sendChatAction?chat_id=' . $Chat . '&action=' . $Status, false, true);
  }

  public function EditText(int $Chat, int $MsgId, string $Msg, array $Markup = null):?object{
    if($Msg === ''):
      $this->Error = self::Error_SendNoMsg;
      return null;
    elseif(strlen($Msg) > self::MsgSizeLimit):
      $this->Error = self::Error_SendMsgTooBig;
      return null;
    endif;
    $temp = '/editMessageText?chat_id=' . $Chat . '&message_id=' . $MsgId . '&text=' . urlencode($Msg) . '&parse_mode=HTML';
    if($Markup !== null):
      $temp .= '&reply_markup=' . urlencode(json_encode($Markup));
    endif;
    return $this->ServerGet($temp, false, true);
  }

  public function EditMarkup(int $Chat, int $MsgId, array $Markup, bool $Async = true):?object{
    return $this->ServerGet('/editMessageReplyMarkup?chat_id=' . $Chat . '&message_id=' . $MsgId . '&reply_markup=' . urlencode(json_encode($Markup)), false, $Async);
  }

  public function SendContact(int $Chat, string $Name, string $Number, string $Vcard = null):?object{
    $temp = '/sendContact?chat_id=' . $Chat . '&phone_number=' . urlencode($Number) . '&first_name=' . urlencode($Name);
    if($Vcard !== null):
      $temp .= '&vcard=' . urlencode($Vcard);
    endif;
    return $this->ServerGet($temp);
  }
}