<?php
// 2021.04.15.01
// Protocol Corporation Ltda.
// https://github.com/ProtocolLive/TelegramBot

class FactoryServer{
  public int $Id = 0;
  public object $Event;
}

class FactoryEventText{
  public int $Type;
  public int $Id;
  public object $User;
  public object $Chat;
  public string $Msg;
  public $Reply;
}

class FactoryEventVoice{
  public int $Type;
  public int $Id;
  public string $File;
}

class FactoryEventImage{
  public int $Type;
  public int $Id;
  public object $User;
  public string $File;
  public string $Minuature;
}

class FactoryEventDocument{
  public int $Type;
  public int $Id;
  public object $User;
  public string $File;
  public string $Name;
}

class FactoryEventGroupMe{
  public int $Type;
  public int $Action;
}

class FactoryEventGroupUpdate{
  public int $Type;
  public int $Action;
}

class FactoryEventCallback{
  public int $Type;
  public int $Id;
  public object $User;
  public string $Data;
}

class FactoryUser{
  public int $Id = 0;
  public bool $Bot;
  public string $Name;
  public $NameLast = null;
  public $NameUser = null;
  public string $Language;
}

class FactoryChat{
  public int $Type;
  public int $Id;
  public string $Name;
  public $NameLast = null;
  public $NameUser = null;
}