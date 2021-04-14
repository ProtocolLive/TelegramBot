<?php
//2021.04.14.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBot

class TelegramBot_Constants{
  public const Event_Text = 1;
  public const Event_Voice = 2;
  public const Event_Image = 3;
  public const Event_Document = 4;
  public const Event_GroupMe = 5;
  public const Event_GroupUpdate = 6;

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
}