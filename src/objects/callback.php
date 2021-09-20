<?php
//2021.09.19.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

//https://core.telegram.org/bots/api#callbackquery

class TblCallback extends TblBasics{
  public int $CallbackId;
  public TblUser $User;
  public TblMsg $Msg;
  public int $ChatInstance;
  public string $Data;
  public ?array $Parameters = null;

  public function __construct(
    array $Data,
    TblData $BotData
  ){
    $this->CallbackId = $Data['id'];
    $this->User = new TblUser($Data['from']);
    $this->Msg = new TblMsg($BotData, $Data['message']);
    $this->ChatInstance = $Data['chat_instance'];
    $pos = strpos($Data['data'], ' ');
    if($pos === false):
      $this->Data = $Data['data'];
    else:
      $this->Data = substr($Data['data'], 0, $pos);
      parse_str(substr($Data['data'], $pos + 1), $this->Parameters);
    endif;
    $this->BotData = $BotData;
  }

  public function ReplyMsg(
    string $Msg,
    bool $Reply = false,
    TblMarkup $Markup = null,
    TblEntities $Entities = null,
    string $ParseMode = null,
    bool $DisablePreview = null,
    bool $DisableNotification = null
  ){
    $this->SendMsg(
      $this->Msg->Chat->Id,
      $Msg,
      $Markup,
      $Entities,
      $Reply? $this->Id : null,
      null,
      $ParseMode,
      $DisablePreview,
      $DisableNotification
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

  public function ReplyEditMarkup(
    TblMarkup $Markup = null
  ){
    $this->EditMarkup(
      $this->Msg->Chat->Id,
      $this->Msg->Id,
      $Markup
    );
  }
}