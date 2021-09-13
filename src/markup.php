<?php
//2021.09.13.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

class TelegramBot_Markup{
  public const Type_Inline = 0;
  public const Type_Keyboard = 1;
  public const Type_KeyboardRemove = 2;
  public const Type_Reply = 3;

  private int $Type;
  private array $Markup = [];

  public function __construct(int $Type){
    $this->Type = $Type;
    if($Type === self::Type_Inline):
      $this->Markup['inline_keyboard'] = [];
    endif;
  }

  public function Get():array{
    return $this->Markup;
  }

  public function ButtonUrl(
    int $Line,
    int $Column,
    string $Text,
    string $Url
  ):bool{
    if($this->Type === self::Type_Inline):
      $this->Markup['inline_keyboard'][$Line][$Column] = [
        'text' => $Text,
        'url' => $Url
      ];
      return true;
    else:
      return false;
    endif;
  }

  public function ButtonLogin(
    int $Line,
    int $Column,
    string $Text,
    string $Url,
    bool $Write = null,
    string $ForwardText = null,
    string $BotName = null
  ):bool{
    if($this->Type === self::Type_Inline):
      $this->Markup['inline_keyboard'][$Line][$Column]['text'] = $Text;
      $this->Markup['inline_keyboard'][$Line][$Column]['login_url']['url'] = $Url;
      if($ForwardText !== null):
        $this->Markup['inline_keyboard'][$Line][$Column]['login_url']['forward_text'] = $ForwardText;
      endif;
      if($BotName !== null):
        $this->Markup['inline_keyboard'][$Line][$Column]['login_url']['bot_username'] = $BotName;
      endif;
      if($Write !== null):
        $this->Markup['inline_keyboard'][$Line][$Column]['login_url']['request_write_access'] = $Write;
      endif;
      return true;
    else:
      return false;
    endif;
  }

  public function ButtonCallback(
    int $Line,
    int $Column,
    string $Text,
    string $Data
  ):bool{
    if($this->Type === self::Type_Inline):
      $this->Markup['inline_keyboard'][$Line][$Column] = [
        'text' => $Text,
        'callback_data' => $Data
      ];
      return true;
    else:
      return false;
    endif;
  }

  public function ButtonQuery(
    int $Line,
    int $Column,
    string $Text,
    string $Query,
    bool $OtherChat = false
  ):bool{
    if($this->Type === self::Type_Inline):
      $this->Markup['inline_keyboard'][$Line][$Column]['text'] = $Text;
      if($OtherChat):
        $this->Markup['inline_keyboard'][$Line][$Column]['switch_inline_query'] = $Query;
      else:
        $this->Markup['inline_keyboard'][$Line][$Column]['switch_inline_query_current_chat'] = $Query;
      endif;
      return true;
    else:
      return false;
    endif;
  }
}