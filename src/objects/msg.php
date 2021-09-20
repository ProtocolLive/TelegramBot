<?php
//2021.09.19.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

//https://core.telegram.org/bots/api#message

class TblMsg extends TblBasics{
  public int $Id;
  public TblUser $User;
  public TblChat $Chat;
  public int $Date;
  public string $Msg;
  public ?TblMsg $Reply;
  public ?TblUser $ForwardFrom;
  public ?int $ForwardDate;
  public ?TblEntities $Entities;

  public function __construct(
    TblData $BotData,
    array $Update = null
  ){
    //null in case of callbacks
    $this->Id = $Update['message_id'];
    $this->User = new TblUser($Update['from']);
    $this->Chat = new TblChat($Update['chat']);
    $this->Date = $Update['date'];
    $this->Msg = $Update['text'];
    $this->BotData = $BotData;
    if(isset($Update['reply_to_message'])):
      $this->Reply = new TblMsg($BotData, $Update['reply_to_message']);
    endif;
    if(isset($Update['forward_from'])):
      $this->ForwardFrom = new TblUser($Update['forward_from']);
      $this->ForwardDate = $Update['forward_date'];
    endif;
    if(isset($Update['entities'])):
      $this->Entities = new TblEntities($Update['entities']);
    endif;
  }

  //https://core.telegram.org/bots/api#sendmessage
  public function ReplyMsg(
    string $Msg,
    bool $Reply = false,
    TblMarkup $Markup = null,
    TblEntities $Entities = null,
    string $ParseMode = null,
    bool $DisablePreview = null,
    bool $DisableNotification = null
  ){
    return $this->SendMsg(
      $this->Chat->Id,
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

  //https://core.telegram.org/bots/api#sendcontact
  public function ReplyContact(
    string $Name,
    string $Phone,
    string $Vcard = null,
    string $NameLast = null,
    TblMarkup $Markup = null,
    bool $Reply = false,
    bool $PreventReplyErr = null,
    bool $DisableNotification = null
  ){
    $this->SendContact(
      $this->Chat->Id,
      $Name,
      $Phone,
      $Vcard,
      $NameLast,
      $Markup,
      $Reply,
      $PreventReplyErr,
      $DisableNotification
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