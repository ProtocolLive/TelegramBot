<?php
//2021.09.18.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

//https://core.telegram.org/bots/api#user

class TblUser{
  public int $Id;
  public bool $Bot;
  public string $Name;
  public ?string $NameLast;
  public ?string $Nick;
  public ?string $Language;
  public ?bool $Groups;
  public ?bool $ReadMsg;
  public ?bool $Inline;

  public function __construct(array $Data){
    $this->Id = $Data['id'];
    $this->Bot = $Data['is_bot'];
    $this->Name = $Data['first_name'];
    $this->NameLast = $Data['last_name'] ?? null;
    $this->Nick = $Data['username'] ?? null;
    $this->Language = $Data['language_code'] ?? null;
    $this->Groups = $Data['can_join_groups'] ?? null;
    $this->ReadMsg = $Data['can_read_all_group_messages'] ?? null;
    $this->Inline = $Data['supports_inline_queries'] ?? null;
  }

  
}