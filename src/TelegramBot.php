<?php
//2021.04.13.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBot

require(__DIR__ . '/constants.php');
require(__DIR__ . '/factorys.php');

class TelegramBot extends TelegramBot_Constants{
  private object $Me;
  private object $Server;
  private string $Url = 'https://api.telegram.org/bot';
  private string $UrlFiles= 'https://api.telegram.org/file/bot';
  private bool $Debug = false;

  private int $Error = 0;
  private string $ErrorMsg = '';
  private array $Errors = [
    self::Error_SendMsgTooBig => 'The message is bigger than ' . self::MsgSizeLimit,
    self::Error_SendNoMsg => 'No message to send',
    self::Error_NoEvent => 'No event to parse',
    self::Error_NoMsg => 'No message to get',
    self::Error_NoFile => 'No file to get'
  ];
  public const Error_SendMsgTooBig = 1;
  public const Error_SendNoMsg = 2;
  public const Error_NoEvent = 3;
  public const Error_NoMsg = 4;
  public const Error_NoFile = 5;

  private const MsgSizeLimit = 4096;

  private function CreateDir(string $Dir, int $Perm = 0755, bool $Recursive = true):void{
    if(is_dir($Dir) === false):
      mkdir($Dir, $Perm, $Recursive);
    endif;
  }

  private function DebugLog(string $File, string $Msg):void{
    $file = SystemDir . '/logs/' . $File . '.log';
    if(file_exists($file)):
      $param = FILE_APPEND;
    else:
      $this->CreateDir(SystemDir . '/logs');
      $param = FILE_TEXT;
    endif;
    file_put_contents($file, $Msg . "\n", $param);
  }

  private function UserParse(array $Server):void{
    $this->Server->Event->User = new FactoryUser();
    $this->Server->Event->User->Id = $Server['message']['from']['id'];
    $this->Server->Event->User->Bot = $Server['message']['from']['is_bot'];
    $this->Server->Event->User->Name = $Server['message']['from']['first_name'];
    $this->Server->Event->User->NameLast = $Server['message']['from']['last_name'] ?? null;
    $this->Server->Event->User->Nick = $Server['message']['from']['username'] ?? null;
    $this->Server->Event->User->Language = $Server['message']['from']['language_code'];
  }
  private function ChatParse(array $Server):void{
    $this->Server->Event->Chat = new FactoryChat();
    if($Server['message']['chat']['type'] === 'private'):
      $this->Server->Event->Chat->Type = self::Chat_Private;
    elseif($Server['message']['chat']['type'] === 'group'):
      $this->Server->Event->Chat->Type = self::Chat_Group;
    endif;
  }

  /**
   * @return object|false
   */
  private function ServerGet($Msg){
    $temp = stream_context_create([
      'http' => [
        'ignore_errors' => true,
        'method' => 'GET',
        'header' => 'User-Agent: Protocol TelegramBot'
      ]
    ]);
    $temp = file_get_contents($this->Url . $Msg, false, $temp);
    $temp = json_decode($temp);
    if($this->Debug):
      $this->DebugLog('send', $this->Url . $Msg);
      $this->DebugLog('send', json_encode($temp, JSON_PRETTY_PRINT));
    endif;
    if($temp->ok === false):
      $this->Error = $temp->error_code;
      $this->ErrorMsg = $temp->description;
      return false;
    else:
      return $temp->result;
    endif;
  }

//---------------------------- Get / Set -----------------------------------------------------

  /**
   * @return object|false
   */
  public function CmdGet(){
    return $this->ServerGet('/getMyCommands');
  }

  /**
   * @return object|false
   */
  public function CmdSet(array $Cmds){
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

  /**
   * @return int|false
   */
  public function Event(){
    if(isset($this->Server->Event->Type) === false):
      $this->Error = self::Error_NoEvent;
      return false;
    else:
      return $this->Server->Event->Type;
    endif;
  }

  public function UserId():int{
    return $this->Server->Event->User->Id;
  }

  public function UserName():string{
    return $this->Server->Event->User->Name;
  }

  public function UserLanguage():string{
    return $this->Server->Event->User->Language;
  }

  public function Msg():string{
    return $this->Server->Event->Msg;
  }

  /**
   * @return int|false
   */
  public function MsgId(){
    if($this->Server->Event->Type === self::Event_Text
    or $this->Server->Event->Type === self::Event_Voice):
      return $this->Server->Event->Id;
    else:
      $this->Error = self::Error_NoMsg;
      return false;
    endif;
  }

  /**
   * @return string|false
   */
  public function File(){
    if($this->Server->Event->Type === self::Event_Voice):
      return $this->Server->Event->File;
    else:
      $this->Error = self::Error_NoFile;
      return false;
    endif;
  }

//--------------------------------------------------------------------------------------

  /**
   * @param string $Token
   * @param int $Debug
   */
  public function __construct(string $Token, bool $Debug = false){
    if(extension_loaded('openssl') === false or extension_loaded('curl') === false):
      return false;
    endif;
    if($Token === ''):
      return false;
    endif;
    $this->Url .= $Token;
    $this->UrlFiles .= $Token;
    $this->Me = $this->ServerGet('/getMe');
    $this->Server = new FactoryServer();
    $this->Debug = $Debug;
  }

  /**
   * @return array|false
   */
  function Error(){
    if($this->Error === 0):
      return false;
    elseif(array_search($this->Error, $this->Errors) === false):
      return [$this->Error, $this->ErrorMsg];
    else:
      return [$this->Error, $this->Errors[$this->Error]];
    endif;
  }

  public function WebhookSet():array{
    $temp = $this->ServerGet('/setWebhook?url=' . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']));
    return json_decode($temp);
  }

  public function WebhookGet():bool{
    $Server = file_get_contents('php://input');
    if($Server === ''):
      return false;
    endif;
    $Server = json_decode($Server, true);
    if($this->Debug):
      $this->DebugLog('webhook', json_encode($Server, JSON_PRETTY_PRINT));
    endif;
    if(isset($Server['message'])):
      if(isset($Server['message']['document'])):
        $this->Server->Event = self::Event_Document;
      elseif(isset($Server['message']['photo'])):
        $this->Server->Event = self::Event_Image;
      elseif(isset($Server['message']['text'])):
        $this->Server->Event = new FactoryEventText();
        $this->Server->Event->Type = self::Event_Text;
        $this->Server->Event->Id = $Server['message']['message_id'];
        $this->Server->Event->Msg = $Server['message']['text'];
        $this->UserParse($Server);
        $this->ChatParse($Server);
      elseif(isset($Server['message']['voice'])):
        $this->Server->Event = new FactoryEventVoice();
        $this->Server->Event->Type = self::Event_Voice;
        $this->Server->Event->Id = $Server['message']['message_id'];
        $this->Server->Event->File = $Server['message']['voice']['file_id'];
        $this->UserParse($Server);
        $this->ChatParse($Server);
      elseif(isset($Server['my_chat_member'])):
        $this->Server->Event = new FactoryEventGroupMe();
        $this->Server->Event->Type = self::Event_GroupMe;
        if($Server['my_chat_member']['new_chat_member']['status'] === 'member'):
          $this->Server->Event->Type->Action = self::GroupMe_Add;
        elseif($Server['my_chat_member']['new_chat_member']['status'] === 'left'):
          $this->Server->Event->Type->Action = self::GroupMe_Quit;
        endif;
        $this->ChatParse($Server);
      elseif(isset($Server['message']['new_chat_members'])):
        $this->Server->Event = new FactoryEventGroupUpdate();
        $this->Server->Event->Type = self::Event_GroupUpdate;
        $this->Server->Event->Action = self::GroupUpdate_Add;
        $this->ChatParse($Server);
      elseif(isset($Server['message']['left_chat_participant'])):
        $this->Server->Event = new FactoryEventGroupUpdate();
        $this->Server->Event->Type = self::Event_GroupUpdate;
        $this->Server->Event->Action = self::GroupUpdate_Quit;
        $this->ChatParse($Server);
      endif;
    endif;
    return true;
  }

//-------------------------------------------------------------------------------------

  /**
   * @return object|false
   */
  function ChatGet(int $User){
    return $this->ServerGet('/getChat?chat_id=' . $User);
  }

  /**
   * @return object|false
   */
  function Send($User, string $Msg, ?array $Markup = null){
    if($Msg === ''):
      $this->Error = self::Error_SendNoMsg;
      return false;
    elseif(strlen($Msg) > self::MsgSizeLimit):
      $this->Error = self::Error_SendMsgTooBig;
      return false;
    endif;
    $temp = '/sendMessage?chat_id=' . $User . '&text=' . urlencode($Msg) . '&parse_mode=HTML';
    if($Markup !== null):
      $temp .= '&reply_markup=' . json_encode($Markup);
    endif;
    return $this->ServerGet($temp);
  }

  /**
   * @return object|false
   */
  function SendVoice(int $User, string $File){
    return $this->ServerGet("/sendVoice?chat_id=$User&voice=$File");
  }

  /**
   * @return object|false
   */
  function Forward(int $To, int $From, int $Msg){
    return $this->ServerGet("/forwardMessage?chat_id=$To&from_chat_id=$From&message_id=$Msg");
  }
}