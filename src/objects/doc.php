<?php
//2021.09.21.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

class TblDoc extends TblBasics{
  public int $MsgId;
  public TblUser $User;
  public TblChat $Chat;
  public string $Id;
  public string $IdUnique;
  public string $Name;
  public string $Mime;
  public int $Size;
  public int $Date;
  public ?string $Caption;
  public ?int $MediaGroup;
  public ?TblImg $Thumb = null;

  public function __construct(
    array $Data,
    TblData $BotData
  ){
    $this->MsgId = $Data['message_id'];
    $this->User = new TblUser($Data['from']);
    $this->Chat = new TblChat($Data['chat']);
    $this->Id = $Data['document']['file_id'];
    $this->IdUnique = $Data['document']['file_unique_id'];
    $this->Name = $Data['document']['file_name'];
    $this->Mime = $Data['document']['mime_type'];
    $this->Size = $Data['document']['file_size'];
    $this->Date = $Data['date'];
    $this->MediaGroup = $Data['media_group_id'] ?? null;
    $this->Caption = $Data['caption'] ?? null;
    if(isset($Data['document']['thumb'])):
      $this->Thumb = new TblImg($Data['document']['thumb']);
    endif;
    $this->BotData = $BotData;
  }

  public function ReplyMsg(
    string $Msg,
    TblMarkup $Markup = null,
    TblEntities $Entities = null,
    string $ParseMode = null,
    bool $DisablePreview = null,
    bool $DisableNotification = null,
    bool $Reply = false,
    bool $Async = false
  ){
    return $this->SendMsg(
      $this->Chat->Id,
      $Msg,
      $Markup,
      $Entities,
      $ParseMode,
      $DisablePreview,
      $DisableNotification,
      $Reply? $this->Id : null,
      null,
      $Async
    );
  }

  public function Forward(int $To){
    $Params['chat_id'] = $To;
    $Params['from_chat_id'] = $this->Chat->Id;
    $Params['message_id'] = $this->Id;
    return $this->ServerMethod('/forwardMessage?' . http_build_query($Params));
  }

  public function Copy(int $To){
    $Params['chat_id'] = $To;
    $Params['from_chat_id'] = $this->Chat->Id;
    $Params['message_id'] = $this->Id;
    return $this->ServerMethod('/copyMessage?' . http_build_query($Params));
  }
}