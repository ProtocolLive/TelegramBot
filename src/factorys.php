<?php
// 2021.04.17.00
// Protocol Corporation Ltda.
// https://github.com/ProtocolLive/TelegramBot

class TelegramBot_FactoryServer{
  public int $Id = 0;
  public object $Event;
}

class TelegramBot_FactoryEventText{
  public int $Type = 0;
  public int $Id = 0;
  public object $User;
  public object $Chat;
  public string $Msg;
  public ?int $Reply = null;
}

class TelegramBot_FactoryEventVoice{
  public int $Type = 0;
  public int $Id = 0;
  public string $File;
}

class TelegramBot_FactoryEventImage{
  public int $Type = 0;
  public int $Id = 0;
  public object $User;
  public string $File;
  public string $Minuature;
}

class TelegramBot_FactoryEventDocument{
  public int $Type = 0;
  public int $Id = 0;
  public object $User;
  public string $File;
  public string $Name;
}

class TelegramBot_FactoryEventGroupMe{
  public int $Type = 0;
  public int $Action = 0;
}

class TelegramBot_FactoryEventGroupUpdate{
  public int $Type = 0;
  public int $Action = 0;
}

class TelegramBot_FactoryEventCallback{
  public int $Type = 0;
  public int $Id = 0;
  public object $User;
  public string $Data;
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