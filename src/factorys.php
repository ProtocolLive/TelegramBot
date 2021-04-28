<?php
// 2021.04.28.00
// Protocol Corporation Ltda.
// https://github.com/ProtocolLive/TelegramBot

abstract class TelegramBot_FactoryEvent{
  public int $Type = 0;
}

class TelegramBot_FactoryServer{
  public int $Id = 0;
  public object $Event;
}

class TelegramBot_FactoryUser{
  public int $Id = 0;
  public bool $Bot;
  public string $Name;
  public ?string $NameLast = null;
  public ?string $NameUser = null;
  public string $Language;
}

class TelegramBot_FactoryChat{
  public int $Type = 0;
  public int $Id = 0;
  public string $Name;
  public ?string $NameLast = null;
  public ?string $NameUser = null;
}

class TelegramBot_FactoryEventText extends TelegramBot_FactoryEvent{
  public int $Id = 0;
  public object $User;
  public object $Chat;
  public string $Msg;
  public ?int $Reply = null;
  public function __construct(){
    $this->Type = TelegramBot_Basics::Event_Text;
    $this->User = new TelegramBot_FactoryUser;
    $this->Chat = new TelegramBot_FactoryChat;
  }
}

class TelegramBot_FactoryEventDice extends TelegramBot_FactoryEvent{
  public int $Id = 0;
  public object $User;
  public object $Chat;
  public string $Emoji;
  public int $Value;
  public function __construct(){
    $this->Type = TelegramBot_Basics::Event_Dice;
    $this->User = new TelegramBot_FactoryUser;
    $this->Chat = new TelegramBot_FactoryChat;
  }
}

class TelegramBot_FactoryEventCommand extends TelegramBot_FactoryEvent{
  public int $Id = 0;
  public object $User;
  public object $Chat;
  public string $Command;
  public ?string $Parameters = null;
  public string $Msg;
  public function __construct(){
    $this->Type = TelegramBot_Basics::Event_Command;
    $this->User = new TelegramBot_FactoryUser;
    $this->Chat = new TelegramBot_FactoryChat;
  }
}

class TelegramBot_FactoryEventVoice extends TelegramBot_FactoryEvent{
  public int $Id = 0;
  public string $File;
  public function __construct(){
    $this->Type = TelegramBot_Basics::Event_Voice;
  }
}

class TelegramBot_FactoryEventImage extends TelegramBot_FactoryEvent{
  public int $Id = 0;
  public object $User;
  public string $File;
  public string $Minuature;
  public function __construct(){
    $this->Type = TelegramBot_Basics::Event_Image;
    $this->User = new TelegramBot_FactoryUser;
  }
}

class TelegramBot_FactoryEventDocument extends TelegramBot_FactoryEvent{
  public int $Id = 0;
  public object $User;
  public string $File;
  public string $Name;
  public function __construct(){
    $this->Type = TelegramBot_Basics::Event_Document;
    $this->User = new TelegramBot_FactoryUser;
  }
}

class TelegramBot_FactoryEventCallback extends TelegramBot_FactoryEvent{
  public int $Id = 0;
  public object $User;
  public object $Chat;
  public string $Data;
  public array $Parameters = [];
  public function __construct(){
    $this->Type = TelegramBot_Basics::Event_Callback;
    $this->User = new TelegramBot_FactoryUser;
    $this->Chat = new TelegramBot_FactoryChat;
  }
}

class TelegramBot_FactoryEventGroupMe extends TelegramBot_FactoryEvent{
  public int $Action = 0;
  public function __construct(){
    $this->Type = TelegramBot_Basics::Event_GroupMe;
  }
}

class TelegramBot_FactoryEventGroupUpdate extends TelegramBot_FactoryEvent{
  public int $Action = 0;
  public function __construct(){
    $this->Type = TelegramBot_Basics::Event_GroupUpdate;
  }
}