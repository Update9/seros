<?php
/*

لتغير حقوق اذا تنشر 
By : @D_C_D_D

*/

ob_start();
$token = 'توكن';
define('API_KEY',$token);
echo "انسى";
function bot($method,$datas=[]){
  $url = "https://api.telegram.org/bot".API_KEY."/".$method;
  $datas = http_build_query($datas);
  $res = file_get_contents($url.'?'.$datas);
  return json_decode($res);
}
function save($array){
    file_put_contents('sales.json', json_encode($array));
}
$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$id = $message->from->id;
$chat_id = $message->chat->id;
$text = $message->text;

$user = $message->from->username;
if(isset($update->callback_query)){
  $chat_id = $update->callback_query->message->chat->id;
  $message_id = $update->callback_query->message->message_id;
  $data     = $update->callback_query->data;
 $user = $update->callback_query->from->username;
}
$admin = ايدي;
$me = bot('getme',['bot'])->result->username;
$sales = json_decode(file_get_contents('sales.json'),1);
# @uuuuhu #
if($chat_id == $admin){
 if($text == '/start'){
  bot('sendMessage',[
   'chat_id'=>$chat_id,
   'text'=>"- مرحباً عزيزي المطور ( @$user ) 🔥.",
   'reply_markup'=>json_encode([
     'inline_keyboard'=>[
       [['text'=>'- اضف سلعة ، 💸','callback_data'=>'add']],
       [['text'=>'- حذف سلعة 🗑\'','callback_data'=>'del']]
      ]
    ])
  ]);
  $sales['mode'] = null;
  save($sales);
 }
 if($data == 'add'){
  bot('editMessageText',[
    'chat_id'=>$chat_id,
    'message_id'=>$message_id,
    'text'=>'• قم بأرسال اسم السلعة ، 📬',
    'reply_markup'=>json_encode([
     'inline_keyboard'=>[
      [['text'=>'- الغاء 🚫!','callback_data'=>'c']]
      ]
    ])
  ]);
  $sales['mode'] = 'add';
  save($sales);
  exit;
 }
 if($text != '/start' and $text != null and $sales['mode'] == 'add'){
  bot('sendMessage',[
   'chat_id'=>$chat_id,
   'text'=>'تم الحفظ ✅. 
~ الان ارسل عدد النقاط ( السعر ) المطلوبة للشراء ، 💸 ... رقم فقط '
  ]);
  $sales['n'] = $text;
  $sales['mode'] = 'addm';
  save($sales);
  exit;
 }
 if($text != '/start' and $text != null and $sales['mode'] == 'addm'){
  $code = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz12345689807'),1,7);
  bot('sendMessage',[
   'chat_id'=>$chat_id,
   'text'=>'تم الحفظ السلعة ✅. 
   ℹ️┇الاسم : '.$sales['n'].'
💵┇السعر : '.$text.'
⛓┇كود السلعة : '.$code
  ]);
  
  $sales['sales'][$code]['name'] = $sales['n'];
  $sales['sales'][$code]['price'] = $text;
  $sales['n'] = null;
  $sales['mode'] = null;
  save($sales);
  exit;
 }
 if($data == 'del'){
  bot('editMessageText',[
    'chat_id'=>$chat_id,
    'message_id'=>$message_id,
    'text'=>'• قم بأرسال كود السلعة ، 📬',
    'reply_markup'=>json_encode([
     'inline_keyboard'=>[
      [['text'=>'- الغاء 🚫!','callback_data'=>'c']]
      ]
    ])
  ]);
  $sales['mode'] = 'del';
  save($sales);
  exit;
 }
 if($text != '/start' and $text != null and $sales['mode'] == 'del'){
  if($sales['sales'][$text] != null){
   bot('sendMessage',[
   'chat_id'=>$chat_id,
   'text'=>'تم حذف السلعة ✅. 
   ℹ️┇الاسم : '.$sales['sales'][$text]['name'].'
💵┇السعر : '.$sales['sales'][$text]['price'].'
⛓┇كود السلعة : '.$text
  ]);
  unset($sales['sales'][$text]);
  $sales['mode'] = null;
  save($sales);
  exit;
  } else {
   bot('sendMessage',[
    'chat_id'=>$chat_id,
    'text'=>'- الكود الذي ارسلته غير موجود 🚫!'
   ]);
  }
 }
} else {
 if(preg_match('/\/(start)(.*)/', $text)){
  $ex = explode(' ', $text);
  if(isset($ex[1])){
   if(!in_array($chat_id, $sales[$chat_id]['id'])){
    $sales[$ex[1]]['collect'] += 1;
    save($sales);
    bot('sendMessage',[
     'chat_id'=>$ex[1] ,
     'text'=>"- قام : @$user بالدخول الى الرابط الخاص وحصلت على نقطة واحده ، ✨\n~ عدد نقاطك : ".$sales[$ex[1]]['collect'], 
    ]);
    $sales[$chat_id]['id'][] = $chat_id;
    save($sales);
   }
  }
  $status = bot('getChatMember',['chat_id'=>'@D_C_D_D','user_id'=>$chat_id])->result->status;
  if($status == 'left'){
   bot('sendMessage',[
       'chat_id'=>$chat_id,
       'text'=>"- لا تستطيع بدء استخدام البوت الا بعد الاشتراك بقناة البوت 🚫' @D_C_D_D",
       'reply_to_message_id'=>$message->message_id,
   ]);
   exit();
  }
  if($sales[$chat_id]['collect'] == null){
   $sales[$chat_id]['collect'] = 0;
   save($sales);
  }
  bot('sendmessage',[
   'chat_id'=>$chat_id,
   'text'=>'
🔱| اهلأ بك عزيزي 👋🏼 .
📮| البوت مخصص لشراء العروض المقدمه في البوت عن طريق تجميع النقاط ، 💵 .
☑| قم بأخيار القسم الذي تريده من الكيبورد 👇🏽.
~ عدد نقاطك : '.$sales[$chat_id]['collect'],

   'reply_markup'=>json_encode([
    'inline_keyboard'=>[
     [['text'=>'- العروض التي يقدمها البوت ، ✨','callback_data'=>'sales']],
     [['text'=>'• تجميع النقاط ، 💸','callback_data'=>'col']],
     [['text'=>'~ تابعنا 🧨.','url'=>'https://t.me/D_C_D_D']]
    ] 
   ])
  ]);
 }
 #  #
 if($data == 'col'){
  bot('editMessageText',[
    'chat_id'=>$chat_id,
    'message_id'=>$message_id,
    'text'=>'- قم بأرسال الرابط ادناه لأصدقائك وكل شخص يدخل تحصل على نقطة واحده  ، ⬇️

https://t.me/'.$me.'?start='.$chat_id.'
💰- اذا كانت طريقه التجميع مستحيله لديك يمكنك مراسله المطور وشراء النقاط ✨
🥀 - @D_C_D_D',
  ]);
 }elseif($data == 'sales'){
  $reply_markup = [];
  $reply_markup['inline_keyboard'][] = [['text'=>'💵┇السعر ','callback_data'=>'s'],['text'=>'ℹ️┇الاسم ','callback_data'=>'s']];
  foreach($sales['sales'] as $code => $sale){
   $reply_markup['inline_keyboard'][] = [['text'=>$sale['price'],'callback_data'=>$code],['text'=>$sale['name'],'callback_data'=>$code]];
  }
  $reply_markup = json_encode($reply_markup);
  bot('editMessageText',[
   'chat_id'=>$chat_id,
   'message_id'=>$message_id,
   'text'=>'- العروض التي يقدمها البوت ، 🔥',
   'reply_markup'=>($reply_markup)
  ]);
  $sales[$chat_id]['mode'] = null;
   save($sales);
   exit;
 } elseif($data == 'yes'){
  $price = $sales['sales'][$sales[$chat_id]['mode']]['price'];
  $name = $sales['sales'][$sales[$chat_id]['mode']]['name'];
  bot('editMessageText',[
   'chat_id'=>$chat_id,
   'message_id'=>$message_id,
   'text'=>"- تم ارسال طلبك لمالك البوت ، ✨\nقم بمراسلته لينفذ طلبك ... @D_C_D_D - @D_C_D_D"
  ]);
  bot('sendmessage',[
   'chat_id'=>$admin,
   'text'=>"@$user \n - قام بشراء $name بسعر $price ، 🧨"
  ]);
  $sales[$chat_id]['mode'] = null;
  $sales[$chat_id]['collect'] -= $price;
  save($sales);
  exit;
 } else {
   if($data == 's') { exit; }
   $price = $sales['sales'][$data]['price'];
   $name = $sales['sales'][$data]['name'];
   if($price != null){
    if($price <= $sales[$chat_id]['collect']){
     bot('editMessageText',[
      'chat_id'=>$chat_id,
      'message_id'=>$message_id,
      'text'=>"هل انت متأكد من شراء $name بسعر $price ؟ ، 🕸",
      'reply_markup'=>json_encode([
       'inline_keyboard'=>[
        [['text'=>'- نعم ، 🔥','callback_data'=>'yes'],['text'=>'- لا 🚫\'','callback_data'=>'sales']] 
       ] 
      ])
     ]);
     $sales[$chat_id]['mode'] = $data;
     save($sales);
     exit;
    } else {
     bot('answercallbackquery',[
      'callback_query_id' => $update->callback_query->id,
      'text'=>'- ليس لديك نقاط كافيه للشراء ، 🚫',
      'show_alert'=>true
     ]);
    }
   }
 }
}
# @D_C_D_D #
$ary = array(299938224,); # <<<== ايديك + ايديات الادمنية  #
$id = $message->from->id;
$admins = in_array($id,$ary);
$data = $update->callback_query->data;
$from_id = $message->from->id;
$chat_id = $message->chat->id;
$chat_id2 = $update->callback_query->message->chat->id;
$cut = explode("\n",file_get_contents("stats/users.txt"));
$users = count($cut)-1;
$mode = file_get_contents("stats/bc.txt");
#Start code 

if ($update && !in_array($id, $cut)) {
    mkdir('stats');
    file_put_contents("stats/users.txt", $id."\n",FILE_APPEND);
  }

    if(preg_match("/(admin)/",$text) && $admins) {
        bot('sendMessage',[
            'chat_id'=>$chat_id,
          'text'=>"
اهلا بك عزيزي *( المطور )* 📻 !
    
اليك كل احصائيات البوت ⚠️
يمكنك استخدام اعدادات بوتك بشكل كامل 
-",
    'reply_to_message_id'=>$message->message_id,
    'parse_mode'=>"MarkDown",
    'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
    [['text'=>'العدد 👥 ','callback_data'=>'users'],['text'=>'ارسال للكل 📩 ','callback_data'=>'set']],
    [['text'=>'حالة البوت 🔋 ','callback_data'=>'stats']],
                ]
                ])
            ]);
    }
    if($data == 'homestats'){
    bot('editMessageText',[
    'chat_id'=>$chat_id2,
    'message_id'=>$message_id,
    'text'=>"
اهلا بك عزيزي *( المطور )* 📻 !
        
اليك كل احصائيات البوت ⚠️
يمكنك استخدام اعدادات بوتك بشكل كامل 
-",
    'reply_to_message_id'=>$message->message_id,
    'parse_mode'=>"MarkDown",
    'disable_web_page_preview'=>true,
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
    [['text'=>'العدد 👥 ','callback_data'=>'users'],['text'=>'ارسال للكل 📩 ','callback_data'=>'set']],
    [['text'=>'حالة البوت 🔋 ','callback_data'=>'stats']],
                ]
                ])
    ]);
    file_put_contents('stats/bc.txt', 'no');
    }
    
    if($data == "users"){ 
        bot('answercallbackquery',[
            'callback_query_id'=>$update->callback_query->id,
            'text'=>"
⚠️ البوت فعال  ☑️ !
عدد المشتركين (  [ $users ] ) !
-",
            'show_alert'=>true,
    ]);
    }
    
    if($data == "set"){ 
        file_put_contents("stats/bc.txt","yas");
        bot('EditMessageText',[
        'chat_id'=>$chat_id2,
        'message_id'=>$update->callback_query->message->message_id,
        'text'=>"
ارسل النص الان 📩 !
ليتم ارسالة الى ( $users ) مشتركاً 👥
ارسل *النص فقط ! * 📄
-
    ",
    'reply_to_message_id'=>$message->message_id,
    'parse_mode'=>"MarkDown",
    'disable_web_page_preview'=>true,
    'reply_markup'=>json_encode([
    'inline_keyboard'=>[
    [['text'=>' الغاء 🚫. ','callback_data'=>'homestats']]    
            ]
        ])
        ]);
    }
    if($text and $mode == "yas" && $admins){
        bot('sendMessage',[
              'chat_id'=>$chat_id,
              'text'=>"
تم ارسال رسالتك بنجاح ❕
وسيتم التوصيل الى ( $users ) 👥 !
-",
    'parse_mode'=>"MarkDown",
    'disable_web_page_preview'=>true,
    'reply_markup'=>json_encode([
    'inline_keyboard'=>[
    [['text'=>'رجوع ','callback_data'=>'homestats']]    
            ]
        ])
    ]);
    for ($i=0; $i < count($cut); $i++) { 
     bot('sendMessage',[
    'chat_id'=>$cut[$i],
    'text'=>"$text",
    'parse_mode'=>"MarkDown",
    'disable_web_page_preview'=>true,
    ]);
    file_put_contents("stats/bc.txt","no");
    } 
    }
    date_default_timezone_set("Asia/Baghdad");
    $getMe = bot('getMe')->result;
    $date = $message->date;
    $gettime = time();
    $sppedtime = $gettime - $date;
    $time = date('h:i');
    $date = date('y/m/d');
    $userbot = "{$getMe->username}";
    $userb = strtoupper($userbot);
    if($data == "stats"){ 
    if ($sppedtime == 3  or $sppedtime < 3) {
    $f = "ممتازة 👏🏻";
    }
    if ($sppedtime == 9 or $sppedtime > 9 ) {
    $f = "لا بأس 👍🏻";
    }
    if ($sppedtime == 10 or $sppedtime > 10) {
    $f = " سئ جدا 👎🏻";
    }
     bot('EditMessageText',[
        'chat_id'=>$chat_id2,
        'message_id'=>$update->callback_query->message->message_id,
        'text' =>"
معلومات البوت 🔋:- 

📄معرف البوت :- @$userb
📈 حالة البوت :- ( $f ) 
⏰ الوقت الان : ( 20$date | $time ) 
-",
'parse_mode'=>"MarkDown",
'disable_web_page_preview'=>true,
    'reply_markup'=>json_encode([
    'inline_keyboard'=>[
    [['text'=>'رجوع ','callback_data'=>'homestats']]    
            ]
        ])
       ]);
    }

# @D_C_D_D #
