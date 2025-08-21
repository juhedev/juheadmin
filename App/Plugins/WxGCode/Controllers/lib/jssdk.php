<?php
namespace Plugins\WxGCode\Controllers\lib;
class JSSDK {
  private $appId;
  private $appSecret;
  // 缓存文件路径（使用绝对路径避免问题）
  private $cacheDir;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
    // 初始化缓存目录（与jssdk.php同目录）
    $this->cacheDir = dirname(__FILE__) . '/';
    // 确保缓存目录可写
    $this->checkCacheDir();
  }

  // 检查缓存目录是否存在且可写
  private function checkCacheDir() {
    if (!is_dir($this->cacheDir)) {
      mkdir($this->cacheDir, 0755, true);
    }
    if (!is_writable($this->cacheDir)) {
      throw new \Exception("缓存目录不可写：{$this->cacheDir}");
    }
  }

  // 检查并创建缓存文件
  private function checkCacheFile($filename) {
    $filePath = $this->cacheDir . $filename;
    // 如果文件不存在则创建并初始化
    if (!file_exists($filePath)) {
      $initialData = json_encode([
        'expire_time' => 0,
        'access_token' => '',
        'jsapi_ticket' => ''
      ]);
      $this->set_php_file($filename, $initialData);
    }
    return $filePath;
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();

    // 动态获取当前URL
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 按ASCII码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
    $signature = sha1($string);

    return [
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    ];
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket() {
    // 先检查并创建缓存文件
    $this->checkCacheFile("jsapi_ticket.php");
    
    $data = json_decode($this->get_php_file("jsapi_ticket.php"));
    
    // 处理空数据或过期情况
    if (empty($data) || $data->expire_time < time()) {
      $accessToken = $this->getAccessToken();
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
      
      if (isset($res->ticket)) {
        $data = new \stdClass(); // 初始化空对象
        $data->expire_time = time() + 7000;
        $data->jsapi_ticket = $res->ticket;
        $this->set_php_file("jsapi_ticket.php", json_encode($data));
      } else {
        throw new \Exception("获取jsapi_ticket失败: " . json_encode($res));
      }
    }
    
    return $data->jsapi_ticket ?? '';
  }

  private function getAccessToken() {
    // 先检查并创建缓存文件
    $this->checkCacheFile("access_token.php");
    
    $data = json_decode($this->get_php_file("access_token.php"));
    
    // 处理空数据或过期情况
    if (empty($data) || $data->expire_time < time()) {
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
      $res = json_decode($this->httpGet($url));
      
      if (isset($res->access_token)) {
        $data = new \stdClass(); // 初始化空对象
        $data->expire_time = time() + 7000;
        $data->access_token = $res->access_token;
        $this->set_php_file("access_token.php", json_encode($data));
      } else {
        throw new \Exception("获取access_token失败: " . json_encode($res));
      }
    }
    
    return $data->access_token ?? '';
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }

  private function get_php_file($filename) {
    $filePath = $this->cacheDir . $filename;
    return trim(substr(file_get_contents($filePath), 15));
  }

  private function set_php_file($filename, $content) {
    $filePath = $this->cacheDir . $filename;
    $fp = fopen($filePath, "w");
    fwrite($fp, "<?php exit();?>" . $content);
    fclose($fp);
  }
}
