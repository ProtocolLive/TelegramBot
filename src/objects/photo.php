<?php
//2021.09.21.01
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

//https://core.telegram.org/bots/api#photosize

class TblImg{
  public string $Id;
  public string $IdUnique;
  public int $Width;
  public int $Height;
  public int $Size;

  public function __construct(array $Data){
    $this->Id = $Data['file_id'];
    $this->IdUnique = $Data['file_unique_id'];
    $this->Width = $Data['width'];
    $this->Height = $Data['height'];
    $this->Size = $Data['file_size'];
  }
}

class TblPhoto extends TblBasics{
  public int $MsgId;
  public TblUser $User;
  public TblChat $Chat;
  public int $Date;
  public ?string $Caption;
  public array $Files = [];
  public ?int $MediaGroup;

  public function __construct(array $Data, TblData $BotData){
    $this->MsgId = $Data['message_id'];
    $this->User = new TblUser($Data['from']);
    $this->Chat = new TblChat($Data['chat']);
    $this->Date = $Data['date'];
    $this->MediaGroup = $Data['media_group_id'] ?? null;
    $this->Caption = $Data['caption'] ?? null;
    foreach($Data['photo'] as $file):
      $this->Files[] = new TblImg($file);
    endforeach;
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
    bool $Async = true
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