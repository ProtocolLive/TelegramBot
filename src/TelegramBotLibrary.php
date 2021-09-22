<?php
//2021.09.22.01
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

require(__DIR__ . '/requires.php');

class TelegramBotLibrary extends TblBasics{
  public function __construct(TblData $BotData){
    if(extension_loaded('openssl') === false):
      trigger_error($this->Errors[TblError::NoSsl], E_USER_ERROR);
    elseif(extension_loaded('curl') === false):
      trigger_error($this->Errors[TblError::NoCurl], E_USER_ERROR);
    endif;
    $this->BotData = $BotData;
  }

  public function MeGet():?TblUser{
    $temp = $this->ServerMethod('/getMe');
    if($temp === null):
      return null;
    else:
      return new TblUser($temp);
    endif;
  }

  //https://core.telegram.org/bots/api#getchat
  public function ChatGet(int $Chat):?TblChat{
    $temp = $this->ServerMethod('/getChat&getChat=' . $Chat);
    if($temp === null):
      return null;
    else:
      return new TblChat($temp);
    endif;
  }

  public function WebhookGet():?object{
    $update = file_get_contents('php://input');
    if($update === ''):
      $this->Error = TblError::NoEvent;
      return null;
    endif;
    $update = json_decode($update, true);
    if(($this->BotData->Debug & TblDebug::Webhook) === TblDebug::Webhook):
      $this->DebugLog(TblDebugLog::Webhook, json_encode($update, JSON_PRETTY_PRINT));
    endif;
    if(($update['message']['entities'][0]['type'] ?? null) === 'bot_command'
    and ($update['message']['entities'][0]['offset'] ?? null) === 0):
      return new TblCmd($update['message'], $this->BotData);
    elseif(isset($update['message']['text'])):
      return new TblMsg($this->BotData, $update['message']);
    elseif(isset($update['message']['photo'])):
      return new TblPhoto($update['message'], $this->BotData);
    elseif(isset($update['message']['document'])):
      return new TblDoc($update['message'], $this->BotData);
    elseif(isset($update['callback_query'])):
      return new TblCallback($update['callback_query'], $this->BotData);
    endif;
  }

  public function CmdGet(
    string $Scope = null,
    string $Language = null,
    int $ScopeChat = null,
    int $ScopeMember = null
  ):?array{
    if($Language !== null):
      $Params['language_code'] = $Language;
    endif;
    if($Scope !== null):
      $array = ['type' => $Scope];
      if($Scope === TblScope::User
      or $Scope === TblScope::Admins
      or $Scope === TblScope::Member):
        $array['chat_id'] = $ScopeChat;
      endif;
      if($Scope === TblScope::Member):
        $array['user_id'] = $ScopeMember;
      endif;
      $Params['scope'] = json_encode($array);
    endif;
    if(isset($Params)):
      return $this->ServerMethod('/getMyCommands?' . http_build_query($Params), false);
    else:
      return $this->ServerMethod('/getMyCommands', false);
    endif;
  }

  public function CmdSet(
    array $Cmds,
    string $Scope = null,
    string $Language = null,
    int $ScopeChat = null,
    int $ScopeMember = null
  ){
    $Params['commands'] = json_encode($Cmds);
    if($Language !== null):
      $Params['language_code'] = $Language;
    endif;
    if($Scope !== null):
      $array = ['type' => $Scope];
      if($Scope === TblScope::User
      or $Scope === TblScope::Admins
      or $Scope === TblScope::Member):
        $array['chat_id'] = $ScopeChat;
      endif;
      if($Scope === TblScope::Member):
        $array['user_id'] = $ScopeMember;
      endif;
      $Params['scope'] = json_encode($array);
    endif;
    return $this->ServerMethod('/setMyCommands?' . http_build_query($Params));
  }

  public function CmdDel(
    string $Language = null,
    string $Scope = null,
    int $ScopeChat = null,
    int $ScopeMember = null
  ){
    if($Language !== null):
      $Params['language_code'] = $Language;
    endif;
    if($Scope !== null):
      $array = ['type' => $Scope];
      if($Scope === TblScope::User
      or $Scope === TblScope::Admins
      or $Scope === TblScope::Member):
        $array['chat_id'] = $ScopeChat;
      endif;
      if($Scope === TblScope::Member):
        $array['user_id'] = $ScopeMember;
      endif;
      $Params['scope'] = json_encode($array);
    endif;
    if(isset($Params)):
      return $this->ServerMethod('/deleteMyCommands?' . http_build_query($Params));
    else:
      return $this->ServerMethod('/deleteMyCommands');
    endif;
  }

  /**
   * @param string $Destination Dir, name and extension
   */
  public function DownloadFile(TblData $BotData, string $FileId, string $Destination):bool{
    $file = $this->ServerMethod('/getFile?file_id=' . $FileId);
    if($file === false):
      return false;
    endif;
    $content = file_get_contents($BotData->UrlFiles . '/' . $file['file_path']);
    file_put_contents($Destination, $content);
    return true;
  }
}