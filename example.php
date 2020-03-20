<?php
/**
 * 宝塔API接口示例Demo
 * 仅供参考，请根据实际项目需求开发，并做好安全处理
 * date 2018/12/12
 * author 阿良
 */
class bt_api {
	private $BT_KEY = "Your Bt key";  //接口密钥
  	private $BT_PANEL = "Your Bt address";	   //面板地址
	
  	//如果希望多台面板，可以在实例化对象时，将面板地址与密钥传入
	public function __construct($bt_panel = null,$bt_key = null){
		if($bt_panel) $this->BT_PANEL = $bt_panel;
		if($bt_key) $this->BT_KEY = $bt_key;
	}
	
  	//示例取面板日志	
	public function GetLogs(){
		//拼接URL地址
		$url = $this->BT_PANEL.'/data?action=getData';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['table'] = 'logs';
		$p_data['limit'] = 10;
		$p_data['tojs'] = 'test';
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	public function GetSystem(){
		//拼接URL地址
		$url = $this->BT_PANEL.'/system?action=GetNetWork';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['table'] = 'logs';
		$p_data['limit'] = 10;
		$p_data['tojs'] = 'test';
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	public function GetDisk(){
		//拼接URL地址
		$url = $this->BT_PANEL.'/system?action=GetDiskInfo';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['table'] = 'logs';
		$p_data['limit'] = 10;
		$p_data['tojs'] = 'test';
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	
  	/**
     * 构造带有签名的关联数组
     */
  	private function GetKeyData(){
  		$now_time = time();
    	$p_data = array(
			'request_token'	=>	md5($now_time.''.md5($this->BT_KEY)),
			'request_time'	=>	$now_time
		);
    	return $p_data;    
    }
  	
  
  	/**
     * 发起POST请求
     * @param String $url 目标网填，带http://
     * @param Array|String $data 欲提交的数据
     * @return string
     */
    private function HttpPostCookie($url, $data,$timeout = 60)
    {
    	//定义cookie保存位置
        $cookie_file='./'.md5($this->BT_PANEL).'.cookie';
        if(!file_exists($cookie_file)){
            $fp = fopen($cookie_file,'w+');
            fclose($fp);
        }
		
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}



//实例化对象
$api = new bt_api();
//获取面板日志
$r_data = $api->GetSystem();
$disk = $api->GetDisk()[0]['size'];
$r_data['disk_total'] = $disk[0];
$r_data['disk_used'] = $disk[1];
$r_data['disk_free'] = $disk[2];
$r_data['disk_percent'] = $disk[3];
//输出JSON数据到浏览器
echo json_encode($r_data);

?>