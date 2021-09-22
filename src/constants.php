<?php
//2021.09.22.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/TelegramBotLibrary

abstract class TblConstants{
  public const MsgSizeLimit = 4096;

  public const Class2Const = [
    'TblMsg' => TblEvents::Text,
    'TblPhoto' => TblEvents::Image,
    'TblDoc' => TblEvents::Document
  ];
}

abstract class TblActions{
  public const Typing = 'typing';
  public const Photo = 'upload_photo';
  public const Video = 'upload_video';
  public const VideoNote = 'upload_video_note';
  public const VideoRec = 'record_video';
  public const VideoRecNote = 'record_video_note';
  public const VoiceRec = 'record_voice';
  public const Voice = 'upload_voice';
  public const Doc = 'upload_document';
  public const Gps = 'find_location';
}

abstract class TblChatType{
  public const User = 'private';
  public const Group = 'group';
  public const GroupSuper = 'supergroup';
  public const Channel = 'channel';
}

abstract class TblDebug{
  public const All = -1;
  public const None = 0;
  public const Webhook = 1;
  public const Send = 2;
}

abstract class TblDebugLog{
  public const Webhook = 0;
  public const Send = 1;
  public const Error = 2;
}

abstract class TblError{
  public const None = 0;
  public const Custom = 1;
  public const NoSsl = 2;
  public const NoCurl = 3;
  public const NoToken = 4;
  public const NoMe = 5;
  public const SendMsgTooBig = 6;
  public const SendNoMsg = 7;
  public const NoEvent = 8;
  public const NoEventMsg = 9;
  public const NoEventDocument = 10;
  public const NoEventImage = 11;
  public const NoEventCallback = 12;
  public const NoFile = 13;
  public const SendTimeout = 14;
  public const CurlError = 15;
}

abstract class TblEvents{
  public const Text = 0;
  public const Image = 1;
  public const Document = 2;
  public const Voice = 3;
}

abstract class TblParse{
  public const Markdown2 = 'MarkdownV2';
  public const Markdown = 'markdown';
  public const Html = 'HTML';
}

//https://core.telegram.org/bots/api#botcommandscope
abstract class TblScope{
  public const Default = 'default';
  public const User_All = 'all_private_chats';
  public const Group_All = 'all_group_chats';
  public const Admins_All = 'all_chat_administrators';
  public const User = 'chat';
  public const Admins = 'chat_administrators';
  public const Member = 'chat_member';
}