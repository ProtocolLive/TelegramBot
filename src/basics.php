<?php
// 2021.04.16.00
// Protocol Corporation Ltda.
// https://github.com/ProtocolLive/TelegramBot

class TelegramBot_Basics{
  protected int $Error = 0;
  protected array $Errors = [
    0 => '',
    self::Error_NoSsl => 'Extension OpenSSL not found',
    self::Error_NoCurl => 'Extension cURL not found',
    self::Error_NoToken => 'No token',
    self::Error_SendMsgTooBig => 'The message is bigger than ' . self::MsgSizeLimit,
    self::Error_SendNoMsg => 'No message to send',
    self::Error_NoEvent => 'No event to parse',
    self::Error_NoEventMsg => 'No message event',
    self::Error_NoEventDocument => 'No document event',
    self::Error_NoEventImage => 'No image event',
    self::Error_NoFile => 'No file to get',
    self::Error_NoRepliedMsg => 'The message its not a reply'
  ];

  protected const MsgSizeLimit = 4096;

  public const Event_Text = 1;
  public const Event_Voice = 2;
  public const Event_Image = 3;
  public const Event_Document = 4;
  public const Event_GroupMe = 5;
  public const Event_GroupUpdate = 6;
  public const Event_CallBack = 7;

  public const Chat_Private = 1;
  public const Chat_Group = 2;

  public const GroupMe_Add = 1;
  public const GroupMe_Quit = 2;
  
  public const GroupUpdate_Add = 1;
  public const GroupUpdate_Quit = 2;

  public const Action_Typing = 'typing';
  public const Action_Photo = 'upload_photo';
  public const Action_Video = 'upload_video';
  public const Action_VideoNote = 'upload_video_note';
  public const Action_VideoRec = 'record_video';
  public const Action_VideoRecNote = 'record_video_note';
  public const Action_VoiceRec = 'record_voice';
  public const Action_Voice = 'upload_voice';
  public const Action_Doc = 'upload_document';
  public const Action_Gps = 'find_location';

  public const Error_NoSsl = 1;
  public const Error_NoCurl = 2;
  public const Error_NoToken = 3;
  public const Error_SendMsgTooBig = 4;
  public const Error_SendNoMsg = 5;
  public const Error_NoEvent = 6;
  public const Error_NoEventMsg = 7;
  public const Error_NoEventDocument = 8;
  public const Error_NoEventImage = 9;
  public const Error_NoFile = 10;
  public const Error_NoRepliedMsg = 11;

  protected function CreateDir(string $Dir, int $Perm = 0755, bool $Recursive = true):void{
    if(is_dir($Dir) === false):
      mkdir($Dir, $Perm, $Recursive);
    endif;
  }

  protected function DebugLog(string $File, string $Msg):void{
    if(file_exists($File)):
      $param = FILE_APPEND;
    else:
      $this->CreateDir(dirname($File));
      $param = FILE_TEXT;
    endif;
    file_put_contents($File, $Msg . "\n", $param);
  }

  protected function Error():?array{
    if($this->Error === 0):
      return null;
    elseif(array_search($this->Error, $this->Errors) === false):
      return [$this->Error, $this->ErrorMsg];
    else:
      return [$this->Error, $this->Errors[$this->Error]];
    endif;
  }
}