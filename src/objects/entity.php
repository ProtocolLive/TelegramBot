<?php
//2021.09.18.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

//https://core.telegram.org/bots/api#messageentity

class TblEntity{
  public string $Type;
  public int $Start;
  public int $Length;
  public ?string $Url;
  public ?int $User;
  public ?string $Language;

  public const Mention = 'mention';
  public const Hashtag = 'hashtag';
  public const Cashtag = 'cashtag';
  public const Command = 'bot_command';
  public const Url = 'url';
  public const Email = 'email';
  public const Phone = 'phone_number';
  public const Bold = 'bold';
  public const Italic = 'italic';
  public const Underline = 'underline';
  public const Strike = 'strikethrough';
  public const Code = 'code';
  public const Pre = 'pre';
  public const TextLink = 'text_link';
  public const MentionText = 'text_mention';

  public function __construct(
    string $Type,
    int $Start,
    int $Length,
    string $Url = null,
    int $User = null,
    string $Language = null
  ){
    $this->Type = $Type;
    $this->Start = $Start;
    $this->Length = $Length;
    $this->Url = $Url;
    $this->User = $User;
    $this->Language = $Language;
  }
}

class TblEntities{
  public array $Entities;

  public function __construct(
    array $Data = null
  ){
    if($Data !== null):
      foreach($Data as $id => $entity):
        $this->Entities[$id] = new TblEntity(
          $entity['type'],
          $entity['offset'],
          $entity['length'],
          $entity['url'] ?? null,
          $entity['user'] ?? null,
          $entity['language'] ?? null
        );
      endforeach;
    endif;
  }

  public function Add(TblEntity $Entity){
    $this->Entities[] = $Entity;
  }

  public function Get():array{
    $temp = [];
    foreach($this->Entities as $id => $entity):
      $temp[$id] = [
        'type' => $entity->Type,
        'offset' => $entity->Start,
        'length' => $entity->Length
      ];
      if($entity->Type === TblEntity::TextLink):
        $temp[$id]['url'] = $entity->Url;
      endif;
      if($entity->Type === TblEntity::Mention):
        $temp[$id]['user'] = $entity->User;
      endif;
      if($entity->Type === TblEntity::Pre):
        $temp[$id]['language'] = $entity->Language;
      endif;
    endforeach;
    return $temp;
  }
}