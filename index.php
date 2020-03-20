<?php
    
    $token = "Your bot token";
    $botname = "Your botusername"; //不要加@
    /*
    
    服务器配置列表
    格式："服务器英文名，需要与下文第一层case'/'后面的名称一致" => "example.php的完整地址，需要通过url访问否则直接输出内容"
    
    */
    $servers = array(
        
        "example" => "http://example.com/example.php",
        
        );
        
    $post = file_get_contents("php://input");
    $post = "[" . $post . "]";
    $data = json_decode($post,true);
    //发送的信息
    $message = $data[0]['message']['text'];
    //chat_id 相当如群号
    $chat_id = $data[0]['message']['chat']['id'];
    //用户id 相当如用户账号
    $userid = $data[0]['message']['from']['id'];
    //messageid 信息标识
    $message_id = $data[0]['message']['message_id'];
    $mg = new message_mg($token);
    $message = explode(" ",$message);
    $command = $message[0];
    $command = str_replace("@$botname","",$command);
    switch($command){
        case '/example':
            switch($message[1]){
                case 'getinfo':
                    $server = str_replace("/","",$message[0]);
                    $editmessageid = $mg -> send_message($chat_id,"正在获取服务器 $server 信息...")['result']['message_id'];
                    $serverinfo = file_get_contents($servers["$server"]);
                    $serverinfo = json_decode($serverinfo,true);
                    $cpunow = $serverinfo['cpu'][2][0] . "%";
                    $memory_total = $serverinfo['mem']['memTotal'];
                    $memory_used = $serverinfo['mem']['memRealUsed'];
                    $memory_free = $serverinfo['mem']['memFree'];
                    $down_ll = $serverinfo['down'] . " KB/S";
                    $up_ll = $serverinfo['up'] . " KB/S";
                    $disk_total = $serverinfo['disk_total'];
                    $disk_used = $serverinfo['disk_used'];
                    $disk_free = $serverinfo['disk_free'];
                    $disk_percent = $serverinfo['disk_percent'];
                    $mes = "服务器 $server 的使用情况:\n\n内存总容量:$memory_total\n已使用:$memory_used\n空闲内存:$memory_free\n\nCPU占用:$cpunow\n\n硬盘总容量:$disk_total\n已使用:$disk_used\n剩余:$disk_free\n使用百分比:$disk_percent\n\n上行:$up_ll\n下行:$down_ll";
                    $mg -> editmes($chat_id,$editmessageid,$mes);
                    break;
                
                default :
                    $mg -> send_message($chat_id,"未找到相关指令");
                    break;
            }
            //$mg -> editmes($chat_id,$editmessageid,"修改文本");
            break;
        //后面可以无限加case(根据上面的servers表格来
        case '/getlist':
            $mes = "";
            $servers_key = array_keys($servers);
            $mes .= "服务器列表:\n";
            foreach($servers_key as $serveritem){
                $serveritem = $serveritem;
                $mes .= "/$serveritem getinfo - $serveritem\n";
            }
            $inlinep = 
                array(
                                    
                    "inline_keyboard" => 
                    array(
                        array(
                            array(
                                "text" => "cxbsoft",
                                "url" => "https://blog.bsot.cn"
                            )
                        )
                    )
                );
            $inlinep = json_encode($inlinep);
            $mes .= "\nCopyright 2020 cxbsoft.All rights reserved";
            $mg -> send_message($chat_id,$mes,$reply="",$inlinep=$inlinep);
            break;
    }
    //$mg -> send_message($chat_id,$message);
    
?>
<?php

    class message_mg{
        function __construct($token){
            $this -> token = $token;
            
        }
        function trimall($str)//删除空格
        {
            $oldchar=array(" ","　","\t","\n","\r");
            $newchar=array("","","","","");
            return str_replace($oldchar,$newchar,$str);
        }
        
        function help_content($command){
            $this -> content = "";
            switch($command){
                case "/":
                    $this -> content = "全局帮助\n/help - 获取帮助\n/ohayo - 早安\n/oyasumi - 晚安\n/signin - 签到";
                case 'signin':
                    $this -> content = "/signin\n用法:\n无参数";
                default:
                    $this -> content = "全局帮助\n/help - 获取帮助\n/ohayo - 早安\n/oyasumi - 晚安\n/signin - 签到";
            }
            return $this -> content;
        }
        
        function get_file_by_id($file_id){
            $token = $this -> token;
            $this -> requesturl = "https://api.telegram.org/bot$token/getFile";
            $pars = array(
                
                "file_id" => "$file_id"
                
                );
            $result = $this -> send_req($this -> requesturl,$pars);
            $result = json_decode($result,true);
            $remote_file_path = "https://api.telegram.org/file/bot$token/" . $result['result']['file_path'];
            $remote_content = file_get_contents($remote_file_path);
            return $remote_content;
            
        }
        
        function get_text($pars,$times=2){
            $this -> resulttext = "";
            if($times == 1){
                
            }
            else{
               array_shift($pars); 
            }
            array_shift($pars);
            foreach($pars as $item){
                $this -> resulttext .= " " . $item ; 
            }
            $resulttext = $this -> resulttext;
            
            return $this -> resulttext;
            
        }
        
        function get_music_url($id){
            $result = $this -> send_req("http://music.163.com/api/song/enhance/player/url?br=128000&ids=[$id]&id=$id",array());
            $result = json_decode($result,true);
            $this -> debug_txt("hello.txt",$this -> result['data'][0]['url']);
            $this -> addr = $result["data"][0]["url"];
            $addr = $this -> addr;
            
            return $this -> addr;
        }
        function debug_txt($filename,$value){
            $f = fopen($filename,'w');
            fwrite($f,$value);
            fclose($f);
        }
        function curl_request($url,$post = '',$cookie = '', $returnCookie = 0) {
          $ip_long = array(
            array('607649792', '608174079'), //36.56.0.0-36.63.255.255
            array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
            array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
            array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
            array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
            array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
            array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
            array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
            array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
            array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
          );
          $rand_key = mt_rand(0, 9);
          $ip = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
          //随机ip
          $header = array(
            "CLIENT-IP: $ip",
            "X-FORWARDED-FOR: $ip",
            "X-Real-IP: $ip"
          );
          $curl = curl_init();
          curl_setopt($curl, CURLOPT_URL, $url);
          curl_setopt($curl, CURLOPT_USERAGENT, 'User-Agent, Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11');
          curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
          curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
          curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
          curl_setopt($curl, CURLOPT_REFERER, "https://music.163.com");
          if ($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
          }
          if ($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
          }
          curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
          curl_setopt($curl, CURLOPT_TIMEOUT, 10);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
          $data = curl_exec($curl);
          if (curl_errno($curl)) {
            return curl_error($curl);
          }
          curl_close($curl);
          if ($returnCookie) {
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie'] = substr($matches[1][0], 1);
            $info['content'] = $body;
            return json_decode($info,true);
          } else {
            return json_decode($data,true);
          }
        }
        function send_req($url,$pars){
            $ch = curl_init();
            //指定URL
            curl_setopt($ch, CURLOPT_URL, $url);
            //设定请求后返回结果
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //声明使用POST方式来进行发送
            curl_setopt($ch, CURLOPT_POST, 1);
            //curl_setopt($curl, CURLOPT_USERAGENT, 'Chrome 42.0.2311.135');
            //发送什么数据呢
            curl_setopt($ch, CURLOPT_POSTFIELDS, $pars);
            //忽略证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            //忽略header头信息
            curl_setopt($ch, CURLOPT_HEADER, 0);
            //设置超时时间
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            //发送请求
            
            $output = curl_exec($ch);
            //关闭curl
            ;
            curl_close($ch);
            //file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$chatid&text=不懂不懂");
            return $output;
        }
        function send_message($chatid,$message,$reply="",$inlinep = ""){
            $token = $this -> token;
            $this -> url = "https://api.telegram.org/bot$token/sendMessage";
            if($reply == ""){
                $this -> req = array(
                    
                    "chat_id" => $chatid,
                    "text" => $message,
                    "disable_web_page_preview" => true,
                    "reply_markup" => $inlinep
                    );
            }
            else{
                $this -> req = array(
                    
                    "chat_id" => $chatid,
                    "text" => $message,
                    "disable_web_page_preview" => true,
                    "reply_to_message_id" => $reply,
                    "reply_markup" => $inlinep
                
                    );
            }
            $this -> result = json_decode($this -> send_req($this -> url,$this -> req),true);
            return $this -> result;
        }
    
        function editmes($chatid,$message_id,$content){
            $token = $this -> token;
            $this -> url = "https://api.telegram.org/bot$token/editMessageText";
            $this -> req = array(
                    
                    "chat_id" => $chatid,
                    "message_id" => (int)$message_id,
                    "text" => "$content"
                    );
            $this -> result = $this -> send_req($this -> url,$this -> req);
            return $this -> result;
        }
        
        function deletemes($chatid,$message_id){
            $token = $this -> token;
            $this -> url = "https://api.telegram.org/bot$token/deleteMessage";
            $this -> req = array(
                    
                    "chat_id" => $chatid,
                    "message_id" => (int)$message_id
                    );
            $this -> result = $this -> send_req($this -> url,$this -> req);
            return $this -> result;
        }
        
        function sendaudio($id_name,$chatid,$message_id="",$file_id="",$inlinep=""){
            $token = $this -> token;
            $url = "https://api.telegram.org/bot$token/sendAudio";
            //$id_name = urlencode($id_name);
            if($file_id == ""){
                if($message_id == ""){
                    $audioad = array(
                                        
                        "chat_id" => $chatid,
                        "audio" => "https://bot.bsot.cn/$token/temp/music/$id_name.mp3",
                        "reply_markup" => $inlinep
                                            
                    );
                    
                }
                else{
                    $audioad = array(
                                        
                        "chat_id" => $chatid,
                        "audio" => "https://bot.bsot.cn/$token/temp/music/$id_name.mp3",
                        "reply_to_message_id" => (int)$message_id,
                        "reply_markup" => $inlinep
                                        
                    );
                    
                    //file_get_contents("https://api.telegram.org/bot1092137381:AAEqVtocp01TELZoUB8ayCasT9f26EDms6g/sendMessage?chat_id=-426558199&text=https://bot.bsot.cn/$token/temp/music/sunc");
                }
            }
            else{
                if($message_id == ""){
                    $audioad = array(
                                        
                        "chat_id" => $chatid,
                        "audio" => "$file_id",
                        "reply_markup" => $inlinep
                                            
                    );
                    
                }
                else{
                    $audioad = array(
                                        
                        "chat_id" => $chatid,
                        "audio" => "$file_id",
                        "reply_to_message_id" => (int)$message_id,
                        "reply_markup" => $inlinep
                                        
                    );
                }
            }
            
            return json_decode($this -> send_req($url,$audioad),true);
            
            
        }
        
        function sendqrcode($photoname,$chatid,$message_id="",$file_id=""){
            $token = $this -> token;
            $url = "https://api.telegram.org/bot$token/sendPhoto";
            $id_name = urlencode($id_name);
            if($file_id == ""){
                if($message_id == ""){
                    $audioad = array(
                                        
                        "chat_id" => $chatid,
                        "photo" => "https://bot.bsot.cn/$token/temp/qrcode/$photoname.png"
                                            
                    );
                    
                }
                else{
                    $audioad = array(
                                        
                        "chat_id" => $chatid,
                        "photo" => "https://bot.bsot.cn/$token/temp/qrcode/$photoname.png",
                        "reply_to_message_id" => (int)$message_id
                                        
                    );
                    
                }
            }
            else{
                if($message_id == ""){
                    $audioad = array(
                                        
                        "chat_id" => $chatid,
                        "photo" => "$file_id"
                                            
                    );
                    
                }
                else{
                    $audioad = array(
                                        
                        "chat_id" => $chatid,
                        "photo" => "$file_id",
                        "reply_to_message_id" => (int)$message_id
                                        
                    );
                }
            }
           
            
            return json_decode($this -> send_req($url,$audioad),true);
            
        }
        
        function sendvoice($id_name,$chatid,$message_id="",$file_id=""){
            $token = $this -> token;
            $url = "https://api.telegram.org/bot$token/sendAudio";
            $id_name = urlencode($id_name);
            if($file_id == ""){
                if($message_id == ""){
                    $audioad = array(
                                        
                        "chat_id" => $chatid,
                        "audio" => "https://bot.bsot.cn/$token/temp/voice/$id_name.mp3"
                                            
                    );
                    
                }
                else{
                    $audioad = array(
                                        
                        "chat_id" => $chatid,
                        "audio" => "https://bot.bsot.cn/$token/temp/voice/$id_name.mp3",
                        "reply_to_message_id" => (int)$message_id
                                        
                    );
                    
                }
            }
            else{
                if($message_id == ""){
                    $audioad = array(
                                        
                        "chat_id" => $chatid,
                        "audio" => "$file_id"
                                            
                    );
                    
                }
                else{
                    $audioad = array(
                                        
                        "chat_id" => $chatid,
                        "audio" => "$file_id",
                        "reply_to_message_id" => (int)$message_id
                                        
                    );
                }
            }
            
            
            return json_decode($this -> send_req($url,$audioad),true);
            
            
        }
        
    }

?>