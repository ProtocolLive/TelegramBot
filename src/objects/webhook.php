<?php
//2021.09.19.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

class TblWebhook extends TblBasics{
    public string $Url;
    public bool $Certificate;
    public int $Pending;
    public string $Ip;
    public ?int $LastErrorDate;
    public ?string $LastErrorMsg;
    public int $MaxConnections;
    public ?array $AllowedUpdate;

  public function __construct(
    TblData $BotData
  ){
    $this->BotData = $BotData;
  }

  public function Register(
    string $Url,
    string $Ip = null,
    int $MaxConnections = null,
    array $AllowedUpdates = null,
    bool $DropPending = null
  ):bool{
    $Params['url'] = $Url;
    if($Ip !== null):
      $Params['ip_address'] = $Ip;
    endif;
    if($MaxConnections !== null):
      $Params['max_connections'] = $MaxConnections;
    endif;
    if($AllowedUpdates !== null):
      $Params['max_connections'] = json_encode($AllowedUpdates);
    endif;
    if($DropPending !== null):
      $Params['drop_pending_updates'] = $DropPending;
    endif;
    return $this->ServerMethod('/setWebhook?' . http_build_query($Params));
  }

  public function Info():void{
    $temp = $this->ServerMethod('/getWebhookInfo');
    $this->Url = $temp['url'];
    $this->Certificate = $temp['has_custom_certificate'];
    $this->Pending = $temp['pending_update_count'];
    $this->Ip = $temp['ip_address'];
    $this->LastErrorDate = $temp['last_error_date'] ?? null;
    $this->LastErrorMsg = $temp['last_error_message'] ?? null;
    $this->MaxConnections = $temp['max_connections'];
    $this->AllowedUpdates = $temp['allowed_updates'] ?? null;
  }

  public function Delete(bool $DropPending = false):bool{
    $temp = '/deleteWebhook';
    if($DropPending):
      $temp .= '?drop_pending_updates=true';
    endif;
    return $this->ServerMethod($temp);
  }
}