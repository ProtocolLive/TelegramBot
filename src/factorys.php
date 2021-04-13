<?php
//2021.04.13.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBot

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
}

class FactoryEventVoice{
  public int $Type;
  public string $File;
}

class FactoryEventGroupMe{
  public int $Type;
  public int $Action;
}

class FactoryEventGroupUpdate{
  public int $Type;
  public int $Action;
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