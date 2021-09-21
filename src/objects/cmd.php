<?php
//2021.09.21.02
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

class TblCmd extends TblBasics{
  public int $Id;
  public TblUser $User;
  public TblChat $Chat;
  public int $Date;
  public string $Command;
  public $Parameters = null;
  public TblEntities $Entities;

  public function __construct(
    array $Data,
    TblData $BotData
  ){
    $this->MsgId = $Data['message_id'];
    $this->User = new TblUser($Data['from']);
    $this->Chat = new TblChat($Data['chat']);
    $this->Date = $Data['date'];
    $pos = strpos($Data['text'], ' ');
    if($pos === false):
      $this->Command = substr($Data['text'], 1);
    else:
      $this->Command = substr($Data['text'], 1, $pos - 1);
      $this->Parameters = substr($Data['text'], $pos + 1);
    endif;
    $this->Entities = new TblEntities($Data['entities']);
    $this->BotData = $BotData;
  }

  public function ReplyMsg(
    string $Msg,
    TblMarkup $Markup = null,
    TblEntities $Entities = null,
    string $ParseMode = null,
    bool $DisablePreview = null,
    bool $DisableNotification = null,
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
      null,
      null,
      $Async
    );
  }

  public function ReplyEditMsg(
    string $Msg,
    TblMarkup $Markup = null,
    TblEntities $Entities = null,
    string $ParseMode = null,
    bool $DisablePreview = null
  ){
    $this->EditMsg(
      $this->Msg->Chat->Id,
      $this->Msg->Id,
      $Msg,
      $Markup,
      $Entities,
      $ParseMode,
      $DisablePreview
    );
  }

  //https://core.telegram.org/bots/api#sendphoto
  public function ReplyPhoto(
    string $Photo,
    string $Caption = null,
    TblMarkup $Markup = null,
    TblEntities $Entities = null,
    string $ParseMode = null,
    bool $DisableNotification = null,
    bool $Async = true
  ){
    $this->SendPhoto(
      $this->Chat->Id,
      $Photo,
      $Caption,
      $Markup,
      $Entities,
      null,
      null,
      $ParseMode,
      $DisableNotification,
      $Async
    );
  }

  //https://core.telegram.org/bots/api#sendcontact
  public function ReplyContact(
    int $Phone,
    string $Name,
    string $Vcard = null,
    string $NameLast = null,
    TblMarkup $Markup = null,
    bool $DisableNotification = null
  ){
    $this->SendContact(
      $this->Chat->Id,
      $Phone,
      $Name,
      $Vcard,
      $NameLast,
      $Markup,
      null,
      null,
      $DisableNotification
    );
  }

  //https://core.telegram.org/bots/api#sendchataction
  public function ReplyAction(string $Action){
    return $this->SendAction(
      $this->Chat->Id,
      $Action
    );
  }
}