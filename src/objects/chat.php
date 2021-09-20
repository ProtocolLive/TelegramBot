<?php
//2021.09.18.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

//https://core.telegram.org/bots/api#chat

class TblChat{
  public int $Id;
  public string $Type;
  public ?string $Nick;
  public ?string $Name;
  public ?string $NameLast;
  public ?string $Photo;
  public ?string $Bio;
  public ?string $Description;
  public ?string $Invite;
  public ?string $Pinned;
  public ?string $Permissions;
  public ?int $Delay;
  public ?int $Delete;
  public ?string $StickerName;
  public ?bool $StickerNameSet;
  public ?int $LinkedChat;
  public ?string $Location;

  public const TypePrivate = 'private';
  public const TypeGroup = 'group';
  public const TypeGroupSuper = 'supergroup';
  public const TypeChannel = 'channel';

  public function __construct(array $Data){
    $this->Id = $Data['id'];
    $this->Type = $Data['type'];
    $this->Nick = $Data['username'] ?? $Data['title'] ?? null;
    $this->Name = $Data['first_name'] ?? null;
    $this->NameLast = $Data['last_name'] ?? null;
    $this->Photo = $Data['photo'] ?? null;
    $this->Bio = $Data['bio'] ?? null;
    $this->Description = $Data['description'] ?? null;
    $this->Invite = $Data['invite_link'] ?? null;
    $this->Pinned = $Data['pinned_message'] ?? null;
    $this->Permissions = $Data['permissions'] ?? null;
    $this->Delay = $Data['slow_mode_delay'] ?? null;
    $this->Delete = $Data['message_auto_delete_time'] ?? null;
    $this->StickerName = $Data['sticker_set_name'] ?? null;
    $this->StickerNameSet = $Data['can_set_sticker_set'] ?? null;
    $this->LinkedChat = $Data['linked_chat_id'] ?? null;
    $this->Location = $Data['location'] ?? null;
  }
}