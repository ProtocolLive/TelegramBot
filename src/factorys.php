<?php
//2021.09.16.02
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

class TelegramBot_FactoryUser{
  public int $Id;
  public bool $Bot;
  public string $Name;
  public ?string $NameLast = null;
  public ?string $NameUser = null;
  public ?string $Language = null;
}

class TelegramBot_FactoryChat{
  public int $Type;
  public int $Id;
  public ?string $Name = null;
}

abstract class TelegramBot_FactoryEvent{
  public ?int $Type = TelegramBot_Basics::Event_Null;
  public object $User;
  public object $Chat;
  public function __construct(){
    $this->User = new TelegramBot_FactoryUser;
    $this->Chat = new TelegramBot_FactoryChat;
  }
}

class TelegramBot_FactoryServer{
  public int $Id;
  public ?object $Event = null;
}

class TelegramBot_FactoryEventText extends TelegramBot_FactoryEvent{
  public int $Id;
  public string $Msg;
  public ?int $Reply = null;
  public function __construct(){
    parent::__construct();
    $this->Type = TelegramBot_Basics::Event_Text;
  }
}

class TelegramBot_FactoryEventDice extends TelegramBot_FactoryEvent{
  public int $Id;
  public string $Emoji;
  public int $Value;
  public function __construct(){
    parent::__construct();
    $this->Type = TelegramBot_Basics::Event_Dice;
  }
}

class TelegramBot_FactoryEventCommand extends TelegramBot_FactoryEvent{
  public int $Id;
  public string $Command;
  public ?string $Parameters = null;
  public string $Msg;
  public function __construct(){
    parent::__construct();
    $this->Type = TelegramBot_Basics::Event_Command;
  }
}

class TelegramBot_FactoryEventVoice extends TelegramBot_FactoryEvent{
  public int $Id;
  public string $File;
  public function __construct(){
    parent::__construct();
    $this->Type = TelegramBot_Basics::Event_Voice;
  }
}

class TelegramBot_FactoryEventImage extends TelegramBot_FactoryEvent{
  public int $Id;
  public string $File;
  public string $Miniature;
  public function __construct(){
    parent::__construct();
    $this->Type = TelegramBot_Basics::Event_Image;
  }
}

class TelegramBot_FactoryEventDocument extends TelegramBot_FactoryEvent{
  public int $Id;
  public string $File;
  public string $Name;
  public function __construct(){
    parent::__construct();
    $this->Type = TelegramBot_Basics::Event_Document;
  }
}

class TelegramBot_FactoryEventCallback extends TelegramBot_FactoryEvent{
  public int $Id;
  public int $CallbackId;
  public string $Data;
  public array $Parameters = [];
  public function __construct(){
    parent::__construct();
    $this->Type = TelegramBot_Basics::Event_Callback;
  }
}

class TelegramBot_FactoryEventGroupMe extends TelegramBot_FactoryEvent{
  public int $Action;
  public function __construct(){
    parent::__construct();
    $this->Type = TelegramBot_Basics::Event_GroupMe;
  }
}

class TelegramBot_FactoryEventGroupUpdate extends TelegramBot_FactoryEvent{
  public int $Action;
  public function __construct(){
    parent::__construct();
    $this->Type = TelegramBot_Basics::Event_GroupUpdate;
  }
}

class TelegramBot_FactoryEventInline{
  public object $User;
  public int $Id;
  public string $ChatType;
  public string $Parameter;
  public function __construct(){
    $this->User = new TelegramBot_FactoryUser;
    $this->Type = TelegramBot_Basics::Event_Inline;
  }
}