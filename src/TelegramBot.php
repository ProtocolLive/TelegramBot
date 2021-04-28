<?php
//2021.04.28.01
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBot

require(__DIR__ . '/basics.php');
require(__DIR__ . '/factorys.php');

class TelegramBot extends TelegramBot_Basics{
  private object $Me;
  private object $Server;
  private string $Url = 'https://api.telegram.org/bot';
  private string $UrlFiles= 'https://api.telegram.org/file/bot';
  private bool $Debug = false;
  private string $DirLogs;

  private function ParseServer(array $Server){
    if(isset($Server['message'])):
      if(isset($Server['message']['text'])):
        if($this->CommandForMe($Server['message']['text'], 0, 1)):
          $this->Server->Event = new TelegramBot_FactoryEventCommand;
          $this->ParseCommand($Server['message']['text']);
        else:
          $this->Server->Event = new TelegramBot_FactoryEventText;
          $this->Server->Event->Reply = $Server['reply_to_message']['message_id'] ?? null;
        endif;
        $this->Server->Event->Msg = $Server['message']['text'];
        $this->Server->Event->Id = $Server['message']['message_id'];
        $this->ParseUser($Server);
        $this->ParseChat($Server);
      elseif(isset($Server['message']['document'])):
        $this->Server->Event = new TelegramBot_FactoryEventDocument;
        $this->Server->Event->Id = $Server['message']['message_id'];
        $this->Server->Event->File = $Server['message']['document']['file_id'];
        $this->Server->Event->Name = $Server['message']['document']['file_name'];
        $this->ParseUser($Server);
      elseif(isset($Server['message']['photo'])):
        $this->Server->Event = new TelegramBot_FactoryEventImage;
        $this->Server->Event->Id = $Server['message']['message_id'];
        $this->Server->Event->Miniature = $Server['message']['photo'][0]['file_id'];
        $this->Server->Event->File = $Server['message']['photo'][1]['file_id'];
        $this->ParseUser($Server);
      elseif(isset($Server['message']['voice'])):
        $this->Server->Event = new TelegramBot_FactoryEventVoice;
        $this->Server->Event->Id = $Server['message']['message_id'];
        $this->Server->Event->File = $Server['message']['voice']['file_id'];
        $this->ParseUser($Server);
        $this->ParseChat($Server);
      elseif(isset($Server['message']['new_chat_members'])):
        $this->Server->Event = new TelegramBot_FactoryEventGroupUpdate;
        $this->Server->Event->Action = self::GroupUpdate_Add;
        $this->ParseChat($Server);
      elseif(isset($Server['message']['left_chat_participant'])):
        $this->Server->Event = new TelegramBot_FactoryEventGroupUpdate;
        $this->Server->Event->Action = self::GroupUpdate_Quit;
        $this->ParseChat($Server);
      elseif(isset($Server['message']['message_auto_delete_timer_changed'])):
        $this->Server->Event = new TelegramBot_FactoryEventGroupMe;
        $this->Server->Event->Type = TelegramBot::GroupMe_AutoClean;
      elseif(isset($Server['message']['dice'])):
        $this->Server->Event = new TelegramBot_FactoryEventDice;
        $this->Server->Event->Emoji = $Server['message']['dice']['emoji'];
        $this->Server->Event->Value = $Server['message']['dice']['value'];
        $this->ParseUser($Server);
        $this->ParseChat($Server);
      endif;
    elseif(isset($Server['my_chat_member'])):
      $this->Server->Event = new TelegramBot_FactoryEventGroupMe;
      if($Server['my_chat_member']['new_chat_member']['status'] === 'member'):
        $this->Server->Event->Type->Action = self::GroupMe_Add;
      elseif($Server['my_chat_member']['new_chat_member']['status'] === 'left'):
        $this->Server->Event->Type->Action = self::GroupMe_Quit;
      endif;
      $this->ParseChat($Server);
    elseif(isset($Server['callback_query'])):
      $this->Server->Event = new TelegramBot_FactoryEventCallback;
      $this->Server->Event->Id = $Server['callback_query']['message']['message_id'];
      $temp = explode(' ', $Server['callback_query']['data']);
      $this->Server->Event->Data = $temp[0];
      if(isset($temp[1])):
        parse_str($temp[1], $this->Server->Event->Parameters);
      endif;
      $this->ParseCallbackUser($Server);
      $this->ParseChat($Server['callback_query']);

    endif;
  }

  private function ParseUser(array $Server):void{
    $this->Server->Event->User->Id = $Server['message']['from']['id'];
    $this->Server->Event->User->Bot = $Server['message']['from']['is_bot'];
    $this->Server->Event->User->Name = $Server['message']['from']['first_name'];
    $this->Server->Event->User->NameLast = $Server['message']['from']['last_name'] ?? null;
    $this->Server->Event->User->Nick = $Server['message']['from']['username'] ?? null;
    $this->Server->Event->User->Language = $Server['message']['from']['language_code'];
  }

  private function ParseCallbackUser(array $Server):void{
    $this->Server->Event->User = new TelegramBot_FactoryUser;
    $this->Server->Event->User->Id = $Server['callback_query']['from']['id'];
    $this->Server->Event->User->Bot = $Server['callback_query']['from']['is_bot'];
    $this->Server->Event->User->Name = $Server['callback_query']['from']['first_name'];
    $this->Server->Event->User->NameLast = $Server['callback_query']['from']['last_name'] ?? null;
    $this->Server->Event->User->Nick = $Server['callback_query']['from']['username'] ?? null;
    $this->Server->Event->User->Language = $Server['callback_query']['from']['language_code'];
  }

  private function ParseChat(array $Server):void{
    if($Server['message']['chat']['type'] === 'private'):
      $this->Server->Event->Chat->Type = self::Chat_Private;
      $this->Server->Event->Chat->Id = $this->Server->Event->User->Id;
    elseif($Server['message']['chat']['type'] === 'group'):
      $this->Server->Event->Chat->Type = self::Chat_Group;
      $this->Server->Event->Chat->Id = $Server['message']['chat']['id'];
      $this->Server->Event->Chat->Name = $Server['message']['chat']['title'];
    endif;
  }

  private function ParseCommand(string $Msg):void{
    $me = '@' . $this->Me->username;
    $len = strlen($me);
    if(substr($Msg, -$len) === $me):
      $Msg = substr($Msg, 0, -$len);
    endif;
    $pos = strpos($Msg, ' ');
    if($pos === false):
      $this->Server->Event->Command = substr($Msg, 1);
      $this->Server->Event->Parameters = null;
    else:
      $this->Server->Event->Command = substr($Msg, 1, $pos - 1);
      $this->Server->Event->Parameters = substr($Msg, $pos + 1);
    endif;
  }

  /**
   * @return array|object|true|null
   */
  private function ServerGet(string $Msg, bool $ReturnArray = false, bool $Async = false){
    $curl = curl_init($this->Url . $Msg);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Protocol SimpleTelegramBot');
    curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . '/cacert.pem');
    if($Async):
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 500);
      curl_setopt($curl, CURLOPT_TIMEOUT_MS, 500);
    endif;
    $temp = curl_exec($curl);
    if($temp === false):
      $this->DebugLog($this->DirLogs . '/debug.log', curl_error($curl));
      $this->Error = self::Error_SendTimeout;
      return null;
    endif;
    $temp = json_decode($temp, $ReturnArray);
    if($this->Debug):
      $this->DebugLog($this->DirLogs . '/send.log', $this->Url . $Msg);
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

    /**
   * If message is a command and is for me
   */
  private function CommandForMe(string $Cmd):bool{
    if(substr($Cmd, 0 ,1) === '/'):
      $pos = strpos($Cmd, '@');
      if($pos === false):
        return true;
      elseif(substr($Cmd, $pos) === '@' . $this->Me->username):
        return true;
      else:
        return false;
      endif;
    else:
      return false;
    endif;
  }

  public function __construct(string $Token, string $DirLogs = __DIR__ . '/logs', bool $Debug = false){
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
      $this->Me = json_decode(file_get_contents(__DIR__ . '/db.json'));
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

  public function CmdSet(array $Cmds):?bool{
    return $this->ServerGet('/setMyCommands?commands=' . json_encode($Cmds));
  }

  public function Name():string{
    return $this->Me->first_name;
  }

  public function Id():int{
    return $this->Me->id;
  }

  public function Nick():string{
    return $this->Me->username;
  }

  public function JoinGroups():bool{
    return $this->Me->can_join_groups;
  }

  public function ReadMsg():bool{
    return $this->Me->can_read_all_group_messages;
  }

  public function InLine():bool{
    return $this->Me->supports_inline_queries;
  }

  public function UserId():int{
    return $this->Server->Event->User->Id;
  }

  public function UserName():string{
    return $this->Server->Event->User->Name;
  }

  public function UserLanguage():?string{
    if(isset($this->Server->Event->User->Language)):
      return $this->Server->Event->User->Language;
    else:
      $this->Error = self::Error_NoLanguage;
      return null;
    endif;
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
    return $this->ServerGet('/setWebhook?url=' . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']));
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
    if($this->Debug):
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
      $temp .= '&reply_markup=' . json_encode($Markup);
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
      if($this->Debug):
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
      $temp .= '&reply_markup=' . json_encode($Markup);
    endif;
    return $this->ServerGet($temp, false, true);
  }

  public function EditMarkup(int $Chat, int $MsgId, array $Markup):?object{
    return $this->ServerGet('/editMessageReplyMarkup?chat_id=' . $Chat . '&message_id=' . $MsgId . '&reply_markup=' . json_encode($Markup), false, true);
  }

  public function SendContact(int $Chat, string $Name, string $Number, string $Vcard = null):?object{
    $temp = '/sendContact?chat_id=' . $Chat . '&phone_number=' . urlencode($Number) . '&first_name=' . urlencode($Name);
    if($Vcard !== null):
      $temp .= '&vcard=' . urlencode($Vcard);
    endif;
    return $this->ServerGet($temp);
  }
}