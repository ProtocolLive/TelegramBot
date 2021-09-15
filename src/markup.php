<?php
//2021.09.14.03
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

class TelegramBot_Markup{
  public const Type_Inline = 0;
  public const Type_InlineRemove = 1;
  public const Type_Keyboard = 2;
  public const Type_KeyboardRemove = 3;
  public const Type_Reply = 4;

  public const Poll_Both = null;
  public const Poll_Quiz = 'quiz';
  public const Poll_Regular = 'regular';

  private int $Type;
  private array $Markup = [];

  public function __construct(int $Type){
    $this->Type = $Type;
    if($Type === self::Type_Inline
    or $Type === self::Type_InlineRemove):
      $this->Markup['inline_keyboard'] = [];
    elseif($Type === self::Type_Keyboard):
      $this->Markup['keyboard'] = [];
    elseif($Type === self::Type_KeyboardRemove):
      $this->Markup['remove_keyboard'] = true;
    elseif($Type === self::Type_Reply):
      $this->Markup['force_reply'] = true;
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

  public function ReplyOptions(
    bool $Selective,
    string $PlaceHolder = null
  ):bool{
    if($this->Type === self::Type_Reply):
      $this->Markup['input_field_placeholder'] = $PlaceHolder;
      $this->Markup['selective'] = $Selective;
      return true;
    else:
      return false;
    endif;
  }

  public function RemoveOptions(
    bool $Selective
  ):bool{
    if($this->Type === self::Type_KeyboardRemove):
      $this->Markup['selective'] = $Selective;
      return true;
    else:
      return false;
    endif;
  }

  public function KeyboardOptions(
    bool $Selective,
    bool $Resize = false,
    bool $OneTime = false,
    string $Placeholder = null,
  ):bool{
    if($this->Type === self::Type_Keyboard):
      $this->Markup['resize_keyboard'] = $Resize;
      $this->Markup['one_time_keyboard'] = $OneTime;
      $this->Markup['input_field_placeholder'] = $Placeholder;
      $this->Markup['selective'] = $Selective;
      return true;
    else:
      return false;
    endif;
  }

  public function ButtonKeyboard(
    int $Line,
    int $Column,
    string $Text,
    bool $Contact = false,
    bool $Location = false,
    string $Pool = null
  ):bool{
    if($this->Type === self::Type_Keyboard):
      $this->Markup['keyboard'][$Line][$Column]['text'] = $Text;
      if($Contact):
        $this->Markup['keyboard'][$Line][$Column]['request_contact'] = $Contact;
      endif;
      if($Location):
        $this->Markup['keyboard'][$Line][$Column]['request_location'] = $Location;
      endif;
      if($Pool !== null):
        $this->Markup['keyboard'][$Line][$Column]['request_poll']['type'] = $Pool;
      endif;
      return true;
    else:
      return false;
    endif;
  }
}