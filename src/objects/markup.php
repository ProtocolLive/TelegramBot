<?php
//2021.09.18.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

class TblMarkup{
  private int $Type;
  private array $Markup = [];
  
  public const TypeInline = 0;
  public const TypeKeyboard = 1;
  public const TypeKeyboardRemove = 2;
  public const TypeReply = 3;

  public const PollBoth = null;
  public const PollQuiz = 'quiz';
  public const PollRegular = 'regular';

  public function __construct(int $Type){
    $this->Type = $Type;
    if($Type === self::TypeInline):
      $this->Markup['inline_keyboard'] = [];
    elseif($Type === self::TypeKeyboard):
      $this->Markup['keyboard'] = [];
    elseif($Type === self::TypeKeyboardRemove):
      $this->Markup['remove_keyboard'] = true;
    elseif($Type === self::TypeReply):
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
    if($this->Type === self::TypeInline):
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
    if($this->Type === self::TypeInline):
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
    if($this->Type === self::TypeInline):
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
    if($this->Type === self::TypeInline):
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
    if($this->Type === self::TypeReply):
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
    if($this->Type === self::TypeKeyboardRemove):
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
    string $Placeholder = null
  ):bool{
    if($this->Type === self::TypeKeyboard):
      $this->Markup['selective'] = $Selective;
      $this->Markup['resize_keyboard'] = $Resize;
      $this->Markup['one_time_keyboard'] = $OneTime;
      if($Placeholder !== null):
        $this->Markup['input_field_placeholder'] = $Placeholder;
      endif;
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
    if($this->Type === self::TypeKeyboard):
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