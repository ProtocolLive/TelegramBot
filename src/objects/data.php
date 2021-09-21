<?php
//2021.09.17.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

class TblData{
  private array $Data = [
    'UrlApi' => 'https://api.telegram.org/bot',
    'UrlFiles' => 'https://api.telegram.org/file/bot',
    'Debug' => TblDebug::None,
    'DirLogs' => __DIR__ . '/logs'
  ];

  public function __construct(
    string $Token,
    string $DirLogs = __DIR__ . '/logs',
    int $Debug = TblDebug::None
  ){
    $this->Data['UrlApi'] .= $Token;
    $this->Data['UrlFiles'] .= $Token;
    $this->Data['Debug'] = $Debug;
    $this->Data['DirLogs'] = $DirLogs;
  }

  public function __get($name){
    return $this->Data[$name];
  }

  //Prevent write
  public function __set($name, $value){}
}