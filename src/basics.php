<?php
//2021.09.21.01
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

class TblBasics{
  protected TblData $BotData;
  protected int $Error = TblError::None;
  protected ?string $ErrorStr = null;

  /**
   * @return null|bool|array
   */
  protected function ServerMethod(
    string $Msg,
    bool $Async = true
  ){
    $temp = $this->BotData->UrlApi . $Msg;
    if(($this->BotData->Debug & TblDebug::Send) === TblDebug::Send):
      $this->DebugLog(TblDebugLog::Send, $temp);
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
      $this->DebugLog(
        TblDebugLog::Error,
        'cURL error #' . curl_errno($curl) . ' ' . curl_error($curl)
      );
      $this->Error = TblError::CurlError;
      return null;
    endif;
    $temp = json_decode($temp, true);
    if(($this->BotData->Debug & TblDebug::Send) === TblDebug::Send):
      $this->DebugLog(TblDebugLog::Send, json_encode($temp, JSON_PRETTY_PRINT));
    endif;
    if($temp['ok'] === false):
      $this->Error = TblError::Custom;
      $this->ErrorStr = $temp['description'];
      return null;
    else:
      return $temp['result'];
    endif;
  }

  //https://core.telegram.org/bots/api#sendmessage
  public function SendMsg(
    int $Chat,
    string $Msg,
    TblMarkup $Markup = null,
    TblEntities $Entities = null,
    string $ParseMode = null,
    bool $DisablePreview = null,
    bool $DisableNotification = null,
    int $Reply = null,
    bool $PreventReplyErr = null,
    bool $Async = true
  ){
    $Params['chat_id'] = $Chat;
    $Params['text'] = $Msg;
    if($Markup !== null):
      $Params['reply_markup'] = json_encode($Markup->Get());
    endif;
    if($Entities !== null):
      $Params['entities'] = json_encode($Entities->Get());
    endif;
    if($Reply !== null):
      $Params['reply_to_message_id'] = $Reply;
    endif;
    if($ParseMode !== null):
      $Params['parse_mode'] = $ParseMode;
    endif;
    if($DisablePreview === true):
      $Params['disable_web_page_preview'] = true;
    endif;
    if($DisableNotification === true):
      $Params['disable_notification'] = true;
    endif;
    if($PreventReplyErr === true):
      $Params['allow_sending_without_reply'] = true;
    endif;
    return $this->ServerMethod('/sendMessage?' . http_build_query($Params), $Async);
  }

  //https://core.telegram.org/bots/api#editmessagetext
  public function EditMsg(
    int $Chat,
    int $MsgId,
    string $Msg,
    TblMarkup $Markup = null,
    TblEntities $Entities = null,
    string $ParseMode = null,
    bool $DisablePreview = null
  ){
    $Params['chat_id'] = $Chat;
    $Params['message_id'] = $MsgId;
    $Params['text'] = $Msg;
    if($Markup !== null):
      $Params['reply_markup'] = json_encode($Markup->Get());
    endif;
    if($Entities !== null):
      $Params['entities'] = json_encode($Entities->Get());
    endif;
    if($ParseMode !== null):
      $Params['parse_mode'] = $ParseMode;
    endif;
    if($DisablePreview === true):
      $Params['disable_web_page_preview'] = true;
    endif;
    return $this->ServerMethod('/editMessageText?' . http_build_query($Params));
  }

  //https://core.telegram.org/bots/api#editmessagereplymarkup
  public function EditMarkup(
    int $Chat,
    int $MsgId,
    TblMarkup $Markup = null
  ){
    $Params['chat_id'] = $Chat;
    $Params['message_id'] = $MsgId;
    if($Markup !== null):
      $Params['reply_markup'] = json_encode($Markup->Get());
    endif;
    return $this->ServerMethod('/editMessageReplyMarkup?' . http_build_query($Params));
  }

  //https://core.telegram.org/bots/api#sendcontact
  public function SendContact(
    int $Chat,
    string $Name,
    string $Phone,
    string $Vcard = null,
    string $NameLast = null,
    TblMarkup $Markup = null,
    int $Reply = null,
    bool $PreventReplyErr = null,
    bool $DisableNotification = null
  ){
    $Params['chat_id'] = $Chat;
    $Params['phone_number'] = $Phone;
    $Params['first_name'] = $Name;
    if($NameLast !== null):
      $Params['last_name'] = $NameLast;
    endif;
    if($Vcard !== null):
      $Params['vcard'] = $Vcard;
    endif;
    if($Markup !== null):
      $Params['reply_markup'] = json_encode($Markup->Get());
    endif;
    if($Reply !== null):
      $Params['reply_to_message_id'] = $Reply;
    endif;
    if($DisableNotification === true):
      $Params['disable_notification'] = true;
    endif;
    if($PreventReplyErr === true):
      $Params['allow_sending_without_reply'] = true;
    endif;
    return $this->ServerMethod('/sendContact?' . http_build_query($Params));
  }

  //https://core.telegram.org/bots/api#sendphoto
  /**
   * @param string $Photo File, FileId or URL
   */
  public function SendPhoto(
    int $Chat,
    string $Photo,
    string $Caption = null,
    TblMarkup $Markup = null,
    TblEntities $Entities = null,
    int $Reply = null,
    bool $PreventReplyErr = null,
    string $ParseMode = null,
    bool $DisableNotification = null
  ){
    $Params['chat_id'] = $Chat;
    if($Caption !== null):
      $Params['caption'] = $Caption;
    endif;
    if($Markup !== null):
      $Params['reply_markup'] = json_encode($Markup->Get());
    endif;
    if($Entities !== null):
      $Params['caption_entities'] = json_encode($Entities->Get());
    endif;
    if($Reply !== null):
      $Params['reply_to_message_id'] = $Reply;
    endif;
    if($PreventReplyErr === true):
      $Params['allow_sending_without_reply'] = true;
    endif;
    if($ParseMode !== null):
      $Params['parse_mode'] = $ParseMode;
    endif;
    if($DisableNotification === true):
      $Params['disable_notification'] = true;
    endif;
    if(file_exists($Photo)):
      $url = $this->BotData->UrlFiles . '/sendPhoto?' . http_build_query($Params);
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . '/cacert.pem');
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, [
        'photo' => new CurlFile($Photo)
      ]);
      curl_setopt($curl, CURLOPT_INFILESIZE, filesize($Photo));
      $temp = curl_exec($curl);
      if($temp === false):
        $this->DebugLog(
          TblDebugLog::Error,
          'cURL error #' . curl_errno($curl) . ' ' . curl_error($curl)
        );
        $this->Error = TblError::CurlError;
        return null;
      else:
        $temp = json_decode($temp);
        if(($this->Debug & TblDebug::Send) === TblDebug::Send):
          $this->DebugLog(TblDebugLog::Send, $url);
          $this->DebugLog(TblDebugLog::Send, json_encode($temp, JSON_PRETTY_PRINT));
        endif;
        if($temp['ok'] === false):
          $this->Error = TblError::Custom;
          $this->ErrorStr = $temp['description'];
          return null;
        else:
          return $temp['result'];
        endif;
      endif;
    else:
      $Params['photo'] = $Photo;
      return $this->ServerMethod('/sendPhoto?' . http_build_query($Params));
    endif;
  }

  //https://core.telegram.org/bots/api#sendchataction
  public function SendAction(int $Chat, string $Action):?bool{
    return $this->ServerMethod('/sendChatAction?chat_id=' . $Chat . '&action=' . $Action);
  }

  protected function DebugLog(
    int $Type,
    string $Msg
  ):void{
    if($Type === TblDebugLog::Error):
      $file = $this->BotData->DirLogs . '/debug.log';
    else:
      $file = $this->BotData->DirLogs . '/class.log';
    endif;
    if(is_file($file)):
      $param = FILE_APPEND;
    else:
      $this->CreateDir(dirname($file));
      $param = null;
    endif;
    file_put_contents($file, $Msg . "\n", $param);
  }

  protected function CreateDir(
    string $Dir,
    int $Perm = 0755,
    bool $Recursive = true
  ):void{
    if(is_dir($Dir) === false):
      mkdir($Dir, $Perm, $Recursive);
    endif;
  }

  public function ErrorGet():?array{
    $ErrorStr = [
      TblError::NoSsl => 'Extension OpenSSL not found',
      TblError::NoCurl => 'Extension cURL not found',
      TblError::NoToken => 'No token',
      TblError::NoMe => 'Could not get bot data',
      TblError::SendMsgTooBig => 'The message is bigger than ' . TblConstants::MsgSizeLimit,
      TblError::SendNoMsg => 'No message to send',
      TblError::NoEvent => 'No event to parse',
      TblError::NoEventMsg => 'No message event',
      TblError::NoEventDocument => 'No document event',
      TblError::NoEventImage => 'No image event',
      TblError::NoEventCallback => 'No callback event',
      TblError::NoFile => 'No file to get',
      TblError::SendTimeout => 'Timeout to get response from server. Maybe the request are been done.',
      TblError::CurlError => 'cURL error. See the logs.'
    ];
    if($this->Error === TblError::None):
      return null;
    elseif($this->Error === TblError::Custom):
      return [TblError::Custom, $this->ErrorStr];
    else:
      return [$this->Error, $ErrorStr[$this->Error]];
    endif;
  }
}