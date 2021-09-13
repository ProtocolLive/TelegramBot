<?php
//2021.09.13.01
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBot

class TelegramBot_Basics{
  protected int $Error = self::Error_None;
  protected array $Errors = [
    self::Error_Custom => '',
    self::Error_NoSsl => 'Extension OpenSSL not found',
    self::Error_NoCurl => 'Extension cURL not found',
    self::Error_NoToken => 'No token',
    self::Error_NoMe => 'Could not get bot data',
    self::Error_SendMsgTooBig => 'The message is bigger than ' . self::MsgSizeLimit,
    self::Error_SendNoMsg => 'No message to send',
    self::Error_NoEvent => 'No event to parse',
    self::Error_NoEventMsg => 'No message event',
    self::Error_NoEventDocument => 'No document event',
    self::Error_NoEventImage => 'No image event',
    self::Error_NoEventCallback => 'No callback event',
    self::Error_NoFile => 'No file to get',
    self::Error_SendTimeout => 'Timeout to get response from server. Maybe the request are been done.'
  ];

  public const MsgSizeLimit = 4096;

  public const Event_Null = 0;
  public const Event_Command = 1;
  public const Event_Text = 2;
  public const Event_Voice = 3;
  public const Event_Image = 4;
  public const Event_Document = 5;
  public const Event_Callback = 6;
  public const Event_GroupMe = 7;
  public const Event_GroupUpdate = 8;
  public const Event_Dice = 9;
  public const Event_Inline = 10;
  public const Event_Edited = 11;

  public const Chat_Private = 1;
  public const Chat_Group = 2;

  public const GroupMe_Add = 1;
  public const GroupMe_Quit = 2;
  public const GroupMe_AutoClean = 3;
  public const GroupMe_Admin = 4;
  public const GroupMe_Kicked = 5;
  
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

  public const InlineChat_Sender = 'sender';
  public const InlineChat_Private = 'private';
  public const InlineChat_Group = 'group';
  public const InlineChat_Channel = 'channel';
  
  public const Scope_Default = 'default';
  public const Scope_Private_All = 'all_private_chats';
  public const Scope_Group_All = 'all_group_chats';
  public const Scope_Admins_All = 'all_chat_administrators';
  public const Scope_Private = 'chat';
  public const Scope_Admins = 'chat_administrators';
  public const Scope_Member = 'chat_member';

  public const Error_None = 0;
  public const Error_Custom = 1;
  public const Error_NoSsl = 2;
  public const Error_NoCurl = 3;
  public const Error_NoToken = 4;
  public const Error_NoMe = 5;
  public const Error_SendMsgTooBig = 6;
  public const Error_SendNoMsg = 7;
  public const Error_NoEvent = 8;
  public const Error_NoEventMsg = 9;
  public const Error_NoEventDocument = 10;
  public const Error_NoEventImage = 11;
  public const Error_NoEventCallback = 12;
  public const Error_NoEventCommand = 13;
  public const Error_NoFile = 14;
  public const Error_SendTimeout = 15;
  public const Error_CurlError = 16;

  public const DebugAll = -1;
  public const DebugNone = 0;
  public const DebugWebhook = 1;
  public const DebugSend = 2;

  public const DebugLog_Webhook = 0;
  public const DebugLog_Send = 1;
  public const DebugLog_Error = 2;

  protected function CreateDir(string $Dir, int $Perm = 0755, bool $Recursive = true):void{
    if(is_dir($Dir) === false):
      mkdir($Dir, $Perm, $Recursive);
    endif;
  }

  protected function DebugLog(int $Type, string $Msg):void{
    if($Type === self::DebugLog_Error):
      $file = $this->DirLogs . '/debug.log';
    else:
      $file = $this->DirLogs . '/class.log';
    endif;
    if(is_file($file)):
      $param = FILE_APPEND;
    else:
      $this->CreateDir(dirname($file));
      $param = null;
    endif;
    file_put_contents($file, $Msg . "\n\n", $param);
  }

  public function Error():?array{
    if($this->Error === self::Error_None):
      return null;
    elseif($this->Error === self::Error_Custom):
      return [$this->Error, $this->Errors[self::Error_Custom]];
    else:
      return [$this->Error, $this->Errors[$this->Error]];
    endif;
  }
}