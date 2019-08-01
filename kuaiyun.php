<?php
/**
 *  author：Hidove 余生
 *  mail：i@abcyun.cc
 *  Blog：blog.hidove.cn
 */
$md5Keys= include './data.php';//载入已上传数据
date_default_timezone_set('Asia/Shanghai');
/**
 * [ 参数说明 ]
 */
$requestUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
$accessKey = "Hidove"; // 用户秘钥对：开通快云存储时的Access_Key，可在会员中心获取
$secretKey = "Hidove"; // 用户秘钥对：开通快云存储时的Secret_Key，可在会员中心获取
$resource = "Hidove"; // 调用来源，联系快云存储协作获取
$voucher = "Hidove"; // 用户通过accesskey和secretkey获取的，可在会员中心点击，以邮件形式获取
$bucketName = "tu.abcyun"; // 用户的空间名称
$token = get_token($voucher,$accessKey,$secretKey,$resource); // 用户操作秘钥，是用户调用api接口时必须用到的的秘钥；可以通过API获取
$secreKey='hidove';//加密秘钥
$domain='https://img-ky.hidove.cn';//cdn域名 需要http://或者https://
$repeatButton = false;//是否开启上传重复查询//推荐关闭

if (!empty($_GET)) {
	$deleteName=decrypt ($_GET['delete'],$secreKey);
	$resultIn=del_file($token,$deleteName,$bucketName,$resource);//删除文件
	$fileUrl=$domain.'/'.$deleteName;
	$md5Keys=array_flip($md5Keys);
	unset($md5Keys[$fileUrl]);
	$md5Keys=array_flip($md5Keys);
	file_put_contents('./data.php',"<?php\treturn ".var_export($md5Keys, true).';');
	die(json_encode([//删除图片后返回结果
		'code'=>'200',
		'msg'=>$resultIn,
		'url'=>$fileUrl],JSON_UNESCAPED_UNICODE));
	}
if(!empty($_FILES['file']['tmp_name']))
{
	$localFile=$_FILES['file']['tmp_name'];
	$md5Key= md5_file($localFile);//计算文件的MD5判断是否重复上传
	if ($repeatButton) {//为true表明执行重复查询
		if (array_key_exists($md5Key, $md5Keys)) {//如果存在重复就不重复上传啦，返回已存在的结果
			$urlarr=parse_url($md5Keys[$md5Key]);
			$urlarr['path']=substr($urlarr['path'],1);
			$secreturl=$requestUrl.'?delete='.urlencode(encrypt($urlarr['path'],$secreKey));
			die(json_encode([//图片已存在，返回md5Keys数组中已存在的图片
			'code'=>'200',
			'msg'=>'file already exist',
			'name'=>$_FILES['file']['name'],
			'url'=>$md5Keys[$md5Key],
			'md5'=>$md5Key,
			'deleteurl'=>$secreturl//经过加密的删除链接，可直接删除储存空间的文件
			],JSON_UNESCAPED_UNICODE));
		}
	}

	$arr = explode('/', $_FILES['file']['type']);
	$fileType=$arr[1];
	if ($arr[0]!='image') {
		die(json_encode([//上传文件不是图片时输出结果
		'code'=>'400',
		'msg'=>'This is not a picture.',
		'name'=>$_FILES['file']['name']],JSON_UNESCAPED_UNICODE));
	}
	if ($fileType=='jpeg') {
		$fileType='jpg';
	}
	$fileName=date('Y-m-d').'/'.uniqid().'.'.$fileType;
	$request = send_file($localFile,$fileName,$token,$bucketName,$resource);
	$fileUrl = get_url($token,$fileName,$bucketName,0,0,$resource);
	$secreturl=$requestUrl.'?delete='.urlencode(encrypt ($fileName,$secreKey));
	if ($repeatButton) {//重复
		$md5Keys= include './data.php';
		$md5Keys[$md5Key]=$fileUrl;
		file_put_contents('./data.php',"<?php\treturn ".var_export($md5Keys,true).';',LOCK_EX );
	}
	die(json_encode([//完美上传成功结果
	'code'=>'200',
	'msg'=>$request,
	'url'=>$fileUrl,
	'deleteurl'=>$secreturl,
	'name'=>$_FILES['file']['name']],JSON_UNESCAPED_UNICODE));
}
die(json_encode([//最终失败
	'code'=>'400',
	'msg'=>'error'],JSON_UNESCAPED_UNICODE));
class URLRequest
{
    public $url;
    public $headers;
    public $params;
    public $body;
    public $expectedFormat;
    public $method;
	public $data;

	/**
	 * [__construct 构造函数，初始化各种东西]
	 * @param [type]  $aUrl     [description]
	 * @param array   $aHeaders [description]
	 * @param array   $aParams  [description]
	 * @param string  $aFormat  [description]
	 * @param boolean $isPost   [description]
	 * @param string  $aBody    [description]
	 */
    public function __construct($aUrl, array $aHeaders, array $aParams, $aFormat = "json", $isPost = false, $aBody = "+")
    {
        $this->url = $aUrl;
        $this->headers = $aHeaders;
        $this->params = $aParams;
        $this->expectedFormat = $aFormat;
        $this->method = ($isPost ? "POST" : "GET");
        $this->body = $aBody;

    }
	/**
	 * [exec curl函数]
	 * @return [type] [description]
	 */
    public function exec()
    {

        $url = $this->url;

        $request = curl_init();
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_HEADER, 1);
        curl_setopt($request, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        if($this->method == "POST")
        {
            curl_setopt($request, CURLOPT_POST, 1);
            curl_setopt($request, CURLOPT_POSTFIELDS, $this->body);
        }

        $response = curl_exec($request);
		$httpCode = curl_getinfo($request, CURLINFO_HTTP_CODE);
		if ($httpCode == '200') {
			$headerSize = curl_getinfo($request, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $headerSize);
			$body = substr($response, $headerSize);
			curl_close($request);
			return $body;
		}else{
			curl_close($request);
			return $response;
		}
    }
}

/**
 * [get_token 获取操作秘钥token函数]
 * @param  [type] $voucher   [description]
 * @param  [type] $accessKey [用户秘钥对：开通快云存储时的Access_Key，可在会员中心获取]
 * @param  [type] $secretKey [用户秘钥对：开通快云存储时的Secret_Key，可在会员中心获取]
 * @param  [type] $resource  [调用来源，联系快云存储协作获取]
 * @return [type]            [用户操作秘钥，是用户调用api接口时必须用到的的秘钥]
 */
function get_token($voucher,$accessKey,$secretKey,$resource){
	$url = "http://api.storagesdk.com/restful/storageapi/storage/getToken"; //获取操作秘钥token方法
	$query = array();
	$data = array();
	$data["voucher"]= $voucher;
	$data["accessKey"]= $accessKey ;
	$data["secretKey"]= $secretKey;
	$data["resource"]= $resource;
	$body = json_encode($data);
	$headers = array("Content-Type: application/json; charset=utf-8");
	$request = new URLRequest($url, $headers, $query, "json", true, $body);
	$response = $request->exec();
	$msg = json_decode($response,true)["message"];
	$arr = explode(":",$msg);
	$token = $arr[1];
	return $token;
}

/**
 * [send_file 上传文件函数]
 * @param  [type] $localFile  [description]
 * @param  [type] $fileName   [description]
 * @param  [type] $token      [description]
 * @param  [type] $bucketName [description]
 * @param  [type] $resource   [description]
 * @return [type]             [description]
 */
function send_file($localFile,$fileName,$token,$bucketName,$resource){
	$url = "http://api.storagesdk.com/restful/storageapi/file/uploadFile"; //上传文件方法
	$query = array();
	$data = array();
	$data['input'] = file_get_contents($localFile);
	$file = base64_encode($fileName);
	$len = strlen(file_get_contents($localFile));
	$headers = array("Content-Type: application/json;charset=utf-8",
		"token:{$token}",
		"fileName:{$file}",
		"bucketName:{$bucketName}",
		"resource:{$resource}",
		"length:{$len}");
	$body = $data["input"];
	$request = new URLRequest($url, $headers, $data, "json", true, $body);
	$response = $request->exec();
	$msg = json_decode($response,true)["message"];
	return $msg;
}

/**
 * [get_url 获取文件的url函数]
 * @param  [string] $token      [description]
 * @param  [string] $fileName   [文件完整路径，例test/demo.png]
 * @param  [string] $bucketName [储存空间地址]
 * @param  [int] $minutes    [获取链接时效；默认是0，即无时效，永久有效]
 * @param  [int] $leng       [是否获取长链接。若链接时效不为0，即为长链接。链接时效为0，默认是leng = 0，即短链接；leng = 1, 是长链接]
 * @param  [type] $resource   [description]
 * @return [type]             [description]
 */
function get_url($token,$fileName,$bucketName,$minutes,$leng,$resource){
	$url = "http://api.storagesdk.com/restful/storageapi/file/getFileUrl"; // 获取文件的url方法
	$query = array();
	$data = array();
	$data["token"]= $token;
	$data["fileName"]= $fileName;
	$data["bucketName"]= $bucketName;
	$data["minutes"]= $minutes;
	$data["leng"]= $leng;
	$data["resource"]="{$resource}";
	$body = json_encode($data);
	//print_r($body);
	$headers = array("Content-Type: application/json; charset=utf-8");
	$request = new URLRequest($url, $headers, $query, "json", true, $body);
	$response = $request->exec();
	$url = json_decode($response,true)["message"];
	return $url;
}
/**
 * [del_file 删除文件函数]
 * @param  [type] $token      [用户操作秘钥，是用户调用api接口时必须用到的的秘钥；可以通过API获取]
 * @param  [type] $fileName   [需要删除的文件名称：目录+文件名]
 * @param  [type] $bucketName [用户的空间名称]
 * @param  [type] $resource   [用户操作秘钥，是用户调用api接口时必须用到的的秘钥]
 * @return [type]             [返回操作结果]
 */
function del_file($token,$fileName,$bucketName,$resource){
	$url = "http://api.storagesdk.com/restful/storageapi/file/deleteFile"; // 删除文件方法
	$query = array();
	$data = array();
	$data["token"] = $token;
	$data["fileName"] = $fileName;
	$data["bucketName"] = $bucketName;
	$data["resource"] = $resource;
	$body = json_encode($data);
	$headers = array("Content-Type: application/json; charset=utf-8");
	$request = new URLRequest($url, $headers, $query, "json", true, $body);
	$response = $request->exec();
	$result	= json_decode($response, true)["message"];
	return $result;
	
}

/**
 * @desc加密
 * @param string $str 待加密字符串
 * @param string $key 密钥
 * @return string
 */
function encrypt($str, $key){
    $mixStr = md5(date('Y-m-d H:i:s').mt_rand(0,1000));
    $tmp = '';
    $strLen = strlen($str);
    for($i=0, $j=0; $i<$strLen; $i++, $j++){
        $j = $j == 32 ? 0 : $j;
        $tmp .= $mixStr[$j].($str[$i] ^ $mixStr[$j]);
    }
    return base64_encode(bind_key($tmp, $key));
}

/**
 * @desc解密
 * @param string $str 待解密字符串
 * @param string $key 密钥
 * @return string
 */
function decrypt($str, $key){
    $str = bind_key(base64_decode($str), $key);
    $strLen = strlen($str);
    $tmp = '';
    for($i=0; $i<$strLen; $i++){
        $tmp .= $str[$i] ^ $str[++$i];
    }
    return $tmp;
}

/**
 * @desc辅助方法 用密钥对随机化操作后的字符串进行处理
 * @param $str
 * @param $key
 * @return string
 */
function bind_key($str, $key){
    $encrypt_key = md5($key);

    $tmp = '';
    $strLen = strlen($str);
    for($i=0, $j=0; $i<$strLen; $i++, $j++){
        $j = $j == 32 ? 0 : $j;
        $tmp .= $str[$i] ^ $encrypt_key[$j];
    }
    return $tmp;
}
?>