<?php
namespace app\common\lib;

use think\db\exception\DbException;
use think\facade\Session;
use think\facade\Cache;
use think\Db;

/**
 * 自定义助手类
 * Class Helper
 * @package app\common\lib
 */
class Helper{
    /**
     * @info 生成指定位数的随机字符串
     * @param int $length 长度
     * @return string
     */
    public function getRandChar($length){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;
        for ($i = 0;
             $i < $length;
             $i++) {
            $str .= $strPol[rand(0, $max)];
        }

        return $str;
    }

    /**
     * @info 格式化时间
     * @param string $time 时间戳
     * @return array
     */
    public function week_of_month($time){
        $wk_day = date('w', $time) ?: 7; //今天周几
        $mondy_time = $time - ($wk_day-1) * 86400;//本周一对应时间戳
        $first_mondy =  strtotime("first Monday", strtotime(date('Y-m-01',$mondy_time)) - 1);//所在月份第1个周一0点的时间戳
        $week_number = intval(($time - $first_mondy) / 86400 / 7) + 1;//得出第几周（从0开始所以要加1）
        return ['year' => date('Y',$mondy_time), 'month' => date('n',$mondy_time), 'week_number' => $week_number];
    }

    /**
     * @info 生成token令牌
     * @return string
     */
    public function createToken(){
        $string = getRandChar(32);
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        return md5($string.$timestamp);
    }

    /**
     * @info 创建用户Token(适用于小程序)
     * @param int $userid 用户id
     * @param int $login_type 1:正常登录 2:微信登录 3:小程序登录
     * @param int $openid 微信openid
     * @param int $unionid 微信unionid
     * @param string $session_key 微信session_key
     * @param int $time 时间戳
     * @return string
     */
    public function createUserToken($userid,$login_type = 3,$openid = 0,$unionid = 0,$session_key = '',$time=43200){
        if(empty($userid)) return false;

        $token = $this->createToken();

        if(!empty($openid)){
            $data = [
                'user_id' => $userid,
                'unionid' => $unionid,
                'session_key' => $session_key,
                'login_type' => $login_type,
            ];
        }else{
            $data = [
                'user_id' => $userid,
                'login_type' => $login_type,
            ];
        }

        Cache::set($token,$data,$time);

        return $token;
    }

    /**
     * @info 下载文件
     * @param string $url 下载地址
     * @param string $absolute_path 文件绝对路径
     * @return true
     */
    public function downloadFile($url, $absolute_path = ''){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
        curl_close($ch);
        $resource = fopen($absolute_path, 'a');
        fwrite($resource, $file);
        fclose($resource);

        return true;
    }

    /**
     * @info 获取微信用户id
     * @param string $wid 微信用户id
     * @return string
     */
    public function getWid($wid = ''){
        if(!empty($wid)){
            return $wid;
        }

        if(!empty($wid = Session::get('wid'))){
            return $wid;
        }

        return $wid;
    }

    /**
     * @info 下载微信头像
     * @param string $url 下载路径
     * @return false|string
     */
    public function downWechatPhoto($url)
    {
        if(empty($url)){
            return false;
        }

        $time = date('Ymd',time()).'/';

        $dir = config('common.upload_public_path').'/Upload/Picture/'.$time;

        if(!is_dir($dir)){
            chmod($dir,0777);
        }

        $save_name = getRandChar(32).'.jpg';
        $full_name = $dir.$save_name;

        $res = $this->downloadFile($url,$full_name);

        if($res){
            return $full_name;
        }

        return false;
    }

    /**
     * 身份证神秘截取
     * @param $idCard
     * @return array|string
     */
    public function getHideIdCard($idCard)
    {
        return substr_replace(substr_replace($idCard,"****",-4 ),"*******",3,7);
    }

    /**
     * 手机号神秘截取
     * @param $mobile
     * @return array|string
     */
    public function getHideMobile($mobile)
    {
        return substr_replace($mobile,"*****",3,5);
    }

    /**
     * 获取身份证信息（一代与二代）获取生日、年龄、性别
     * @param string $idcard
     * @return array
     * @throws \Exception
     */
    public function getIdCardInfo($idcard)
    {
        if(empty($idcard) || $idcard[0] == 0){
            return ['birthday'=>'0000-00-00','age'=>0,'sex'=>0,'res'=>0];
        }

        $len = strlen($idcard);

        //二代身份证
        if($len == 18){
            $one = [1,3,5,7,9];
            $two = [0,2,4,6,8];

            $birthday = substr($idcard,6,8);
            $str_to_time = strtotime($birthday);
            $birthday = date('Y-m-d',$str_to_time);

            $check_res = preg_match("/^[1-2][\d]{3}\-(0\d|1[0-2])\-([0-2]\d|3[0-1])$/",$birthday);

            if($check_res == 0){
                return ['birthday'=>'0000-00-00','age'=>0,'sex'=>0,'res'=>0];
            }

            $sex = substr($idcard,16,1);

            if(in_array($sex,$one)){
                $sex = 1;
            }else if(in_array($sex,$two)){
                $sex = 2;
            }else{
                $sex = 0;
            }

            $date = new \DateTime($birthday);
            $now = new \DateTime();
            $interval = $now->diff($date);
            $age = $interval->y;

        }else{
            return ['birthday'=>'0000-00-00','age'=>0,'sex'=>0,'res'=>0];
        }

        return ['birthday'=>$birthday,'age'=>$age,'sex'=>$sex,'res'=>1];
    }

    /**
     * 密码加盐
     * @param $password
     * @return array
     */
    public function passwordAddSalt($password)
    {
        if(empty($password)){
            return ['password'=>$password,'salt'=>''];
        }

        $salt = substr(uniqid(),8,6);

        $password = md5($password.$salt);

        return ['password'=>$password,'salt'=>$salt];

    }

    /**
     * @info 地区的查找
     * @param array $list
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param int $root
     * @return array
     */
    public function getTree(array $list, $pk = 'id', $pid = 'parentid', $child = 'child', $root = 0)
    {
        $tree = [];
        foreach ($list as $key => $val) {
            if ($val[$pid] == $root) {
                //获取当前$pid所有子类
                unset($list[$key]);
                if (!empty($list)) {
                    $child = $this->getTree($list, $pk, $pid, $child, $val[$pk]);
                    if (!empty($child)) {
                        $val['child'] = $child;
                    }
                }
                $tree[] = $val;
            }
        }
        return $tree;
    }

    /**
     * @info 改变配置文件数据格式
     * @param array $data
     * @return array
     */
    public function _setArrayFormat(array $data)
    {
        $arr = [];
        foreach ($data as $k=>$v){
            $tamp['value'] = $k;
            $tamp['label'] = $v;

            array_push($arr, $tamp);
        }

        return $arr;
    }

    /**
     * @info 发送验证码
     * @param $mobile
     * @param int $messType
     * @return array
     */
    public function sendcode($mobile,$messType=2) {

        $code = rand(100000,999999);
        switch($messType){
            case 1:
                $message = '验证码：'.$code.'，亲爱的志愿者朋友，团团欢迎您回家！';
                break;
            case 2:
                $message = '验证码为:'.$code.'。请按照页面提示完成验证,如非本人操作，请忽略本短信';
                break;
        }

        if(config('common.sendMobileMessage')==1){
            $redis = \think\facade\Cache::handler();
            $redis ->select(config('queue.select'));

            $res =  $redis->lPush('yiWangSendCode',json_encode(['mobile'=>$mobile,'message'=>$message,'time'=>time()+40]));

            if(!$res){
                return ['result'=>-1,'msg'=>'发送失败'];
            }
        }else{
            $data = [];
            $data['key'] = 'dev';
            $data['mobile'] = \org\Rsa::publicEncrypt($mobile,'dev');
            $data['message'] = $message;
            $this->curl_post('https://bgapi.54heb.com/sendmess',$data);
        }

        Db::name('verifcode')->insert(['userid'=>0,'account'=>$mobile,'createtime'=>date('Y-m-d H:i:s', time()),'code'=>$code,'status'=>0]);
        return ['result'=>1,'msg'=>'发送成功'];
    }

    /**
     * @info 模拟表单提交(get)
     * @param string $url
     * @return false|array
     */
    function curl_get($url){
        if(empty($url)){
            return false;
        }
        $output = '';

        $ch = curl_init();
        $str =$url;
        curl_setopt($ch, CURLOPT_URL, $str);
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        $output = json_decode($output,1);
        return $output;
    }

    /**
     * @info 模拟表单提交(post)
     * @param string $url
     * @param array $data
     * @return string|bool
     */
    function curl_post($url, $data=[]){
        if (!$url) {
            return false;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    /**
     移动：134、135、136、137、138、139、150、151、157(TD)、158、159、187、188
     联通：130、131、132、152、155、156、185、186
     电信：133、153、180、189、（1349卫通）
     * 手机服务商函数 getphonetype
     * @param  string  $phone   手机号字符串
     * @return  int   1中国移动，2中国联通  3中国电信  4未知
     */
    public function getphonetype($phone){
        //20200331改成统一端口发送短信
        return 2;
        $phone = trim($phone);
        $isSpecialMobile = Db::name('special_mobile')->where('mobile',$phone)->find();
        if($isSpecialMobile){
            return 1;
        }

        $isChinaMobile = "/^134[0-8]\d{7}$|^(?:13[5-9]|147|15[0-27-9]|178|18[2-478]|198)\d{8}$/"; //移动方面最新答复
        $isChinaUnion = "/^(?:13[0-2]|145|15[56]|176|18[56])\d{8}$/"; //向联通微博确认并未回复
        $isChinaTelcom = "/^(?:133|153|177|173|18[019])\d{8}$/"; //1349号段 电信方面没给出答复，视作不存在
        if(preg_match($isChinaMobile, $phone)){
            return 1;
        }elseif(preg_match($isChinaUnion, $phone)){
            return 2;
        }elseif(preg_match($isChinaTelcom, $phone)){
            return 3;
        }else{
            return 4;
        }
    }

    /**
     * @info 根据时间获取活动状态
     * @param $sign_start_time
     * @param $sign_end_time
     * @param $start_time
     * @param $end_time
     * @return int
     */
    public function getActivityStatus($sign_start_time,$sign_end_time,$start_time,$end_time){
        $nowtime = time();
        $sign_start_time = strtotime($sign_start_time);
        $sign_end_time = strtotime($sign_end_time);
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);

        if($nowtime > $sign_start_time && $nowtime < $sign_end_time){
            $statu = 1;  //报名中
        }elseif($nowtime > $start_time && $nowtime < $end_time){
            $statu = 2;  //进行中
        }elseif ($nowtime > $end_time){
            $statu = 3;  //已完成
        }else{
            $statu = 4; //预热中
        }
        return $statu;
    }

    /**
     * @info 接口日志
     * @param $name
     * @param $other
     */
    public function funDebug($name,$other)
    {
        $param = input();

        $date_time = date('Y-m-d H:i:s',time());

        trace('接口名称：'.$name.'-&- 时间：'.$date_time.'-&- 参数：'.json_encode($param).'-&- 其他说明：'.$other);

    }

    /**
     * @info openssl加密
     * @param $id
     * @param string $key
     * @return string
     */
    public function encrypt($id,$key='!Qw89Kl$d.kd^@*)'){
        if(empty($key)){
            return '密钥不能为空';
        }

        $iv='kjm983KjW$%)^=(!';
        $data['value']=base64_encode(openssl_encrypt($id, 'AES-128-CBC',$key, 1 , $iv));
        $encrypt=base64_encode($data['value']);
        return $encrypt;
    }

    /**
     * @info openssl解密
     * @param $encrypt
     * @param string $key
     * @return string
     */
    public function decrypt($encrypt,$key='!Qw89Kl$d.kd^@*)')
    {
        if(empty($key)){
            return '密钥不能为空';
        }
        $encrypt = base64_decode($encrypt);
        $iv='kjm983KjW$%)^=(!';
        $decrypt = openssl_decrypt(base64_decode($encrypt), 'AES-128-CBC', $key, 1 , $iv);
        $id = $decrypt;

        if($id){
            return $id;
        }else{
            return 0;
        }
    }

    /**
     * @info openssl_str
     * @param string $str
     * @return string
     */
    public function openssl_str($str)
    {
        $str_len = strlen($str);

        for($i=1;$i<11;$i++){
            $shiliu_double = 16 * $i;

            if($shiliu_double > $str_len){
                $set_number = $shiliu_double - $str_len;

                $blank_str = '                                                                      ';

                $new_blank = substr($blank_str,0,$set_number);

                $str .= $new_blank;

                break;
            }else if($shiliu_double == $str_len){
                break;
            }


        }

        return $str;
    }

    /**
     * @info openssl加密（新版）
     * @param $id
     * @param string $key
     * @param string $iv
     * @param int $level
     * @return string
     */
    public function encrypt2($id,$key='*)9Kl$d.!Qw8kd^@',$iv='%)^=(!KjW$kjm983',$level = 1){
        if(empty($key)){
            return '密钥不能为空';
        }

        //    $id = openssl_str($id);
        $id = str_pad($id,16,' ');

        $data['value']=base64_encode(openssl_encrypt($id, 'AES-128-CBC',$key, $level , $iv));
        $encrypt=base64_encode($data['value']);
        return $encrypt;
    }

    /**
     * @info openssl解密（新版）
     * @param $encrypt
     * @param string $key
     * @param string $iv
     * @param int $level
     * @return int
     */
    public function decrypt2($encrypt,$key='*)9Kl$d.!Qw8kd^@',$iv='%)^=(!KjW$kjm983',$level = 1)
    {
        if(empty($key)){
            return '密钥不能为空';
        }
        $encrypt = base64_decode($encrypt);
        $decrypt = openssl_decrypt(base64_decode($encrypt), 'AES-128-CBC', $key, $level , $iv);
        $id = $decrypt;

        if($id){
            return $id;
        }else{
            return 0;
        }
    }

    /**
     * @info
     * @param array $params
     * @return string
     */
    public function assemble($params)
    {
        if(!is_array($params))  return null;
        ksort($params, SORT_STRING);
        $sign = '';
        foreach($params AS $key=>$val){
            if(is_null($val))   continue;
            if(is_bool($val))   $val = ($val) ? 1 : 0;
            $sign .= $key . (is_array($val) ? $this->assemble($val) : $val);
        }
        return $sign;

    }

    /**
     * 添加关系转接内容数组
     * @param array $arr 消息数组
     * @param string $time 时间，不传则为当前时间
     * @param string $content 需要添加的内容
     * @return array|mixed|string
     */
    public function pushLeagueLog($arr = [], $time = '', $content = '')
    {
        if(empty($arr)) return $arr;

        if(empty($time)) $time = date('Y-m-d H:i:s',time());

        if(empty($content)) return $arr;

        $arr = json_decode($arr,1);

        if(!is_array($arr)) return $arr;

        $arr[] = ['time'=>$time,'content'=>$content];

        return json_encode($arr);

    }

    /**
     * redis 锁住某个操作
     * @param string $sign 签名标识
     * @param int $expire 锁时间
     * @param bool $isMsec 毫秒锁 ,默认 false 不启用
     * @param bool $isPrepend 令牌是否追加操作地址，默认为true启用
     * @param string $prefix 锁前缀
     * @return boolean
     */
    public function redisLock($sign, $expire = 1, $isMsec = false, $isPrepend = true, $prefix = 'lock_'){
        //abc为操作的控制器   def为操作的方法
        $url = $isPrepend ? strtolower(request()->controller() . '/' . request()->action()) : '';//操作地址
        $key = $prefix . md5($url . $sign);//令牌键值
        $redis = new \org\AccessRate();

        return $redis->setLock($key, time(), $expire, $isMsec, true);
    }

    /**
     * 删除redis锁
     * @param $key
     * @return boolean
     */
    public function redisDelLock($key){
        $redis = new \org\AccessRate();
        return $redis->removeLock($key,true);
    }

    /**
     * 图片输出为base64二进制流
     * @param $img_file
     * @return string
     */
    public function imgToBase64($img_file)
    {
        $img_base64='';
        if(file_exists($img_file)){
            $app_img_file = $img_file; //图片路径
            $img_info = getimagesize($app_img_file); //取得图片的大小， 类型等
            $fp = fopen($app_img_file,'r'); //图片是否可读权限
            if($fp){
                $filesize=filesize($app_img_file);
                $content=fread($fp,$filesize);
                $file_content=chunk_spLit(base64_encode($content)); //base 64编码

                switch($img_info[2]){//判读图片类型
                    case 1:
                        $img_type='gif';
                        break;

                    case 2:
                        $img_type='jpg';
                        break;

                    case 3:
                        $img_type='png';
                        break;

                }

                $img_base64 = 'data:image/'.$img_type.';base64,'.$file_content; //合成图片的base 64编码
            }

            fclose($fp);
        }
        return $img_base64;//返回图片的base 64
    }

    /**
     * @info 创建uuid
     * @param string $prefix
     * @return string
     */
    public function create_uuid($prefix = ""){
        $str = md5(uniqid(mt_rand(), true));
        $uuid  = substr($str,0,8) . '-';
        $uuid .= substr($str,8,4) . '-';
        $uuid .= substr($str,12,4) . '-';
        $uuid .= substr($str,16,4) . '-';
        $uuid .= substr($str,20,12);
        return $prefix . $uuid;
    }

    /**
     * @info 截取字符串
     * @param $user_name
     * @return string
     */
    public function substr_cut($user_name){
        $strlen     = mb_strlen($user_name, 'utf-8');
        if($strlen<2) return $user_name;
        $firstStr     = mb_substr($user_name, 0, 1, 'utf-8');
        $lastStr     = mb_substr($user_name, -1, 1, 'utf-8');
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    }

    /**
     * @info 经纬度算距离
     * @param $lat1
     * @param $lat2
     * @param $lng1
     * @param $lng2
     * @return float
     */
    function get_two_point_distance($lat1,$lat2,$lng1,$lng2)
    {
        $radLat1 = deg2rad($lat1);//deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;
        return round($s,2);//返回公里数
    }

    /**
     * @info 上传并验证图片到服务器
     * @return array
     */
    public function uploadPicture()
    {
        $file   = request()->file('file');
        if(empty($file)){
            return ['code'=>1001,'msg'=>'文件不存在','data'=>[]];
        }

        try {
            $dir = config('common.upload_public_path').'/Upload/Picture';

            if(!is_dir($dir)){
                $dir_res = mkdir($dir,0777,true);
                chmod($dir,0777);
            }

            if(empty($dir_res)){
                trace('创建图片目录失败:'.json_encode($dir),'ERROR');
            }

            $info = $file->validate(['size'=>52428800,'ext'=>'jpg,png,gif,jpeg'])->move($dir);

            if($info){
                $save_name = $info->getSaveName();
                return ['code'=>0,'msg'=>'图片上传成功','data'=>['save_path'=>$save_name,'path'=>config('common.pic_domain')]];
            }

            $error_info = $file->getError();

            return  ['code'=>1001,'msg'=>'','data'=>$error_info];

        } catch (\Exception $e) {
            return ['code'=>1001,'msg'=>'','data'=>$e->getMessage()];
        }
    }
    /**
     * @info 上传并验证Excel到服务器
     * @return array
     */
    public function uploadExcel()
    {
        $file   = request()->file('file');

        if(empty($file)){
            return ['code'=>1001,'msg'=>'文件不存在','data'=>[]];
        }

        $dir = '/mnt/nfsroot/protected/Excel/';

        if(!is_dir($dir)){
            $dir_res = mkdir($dir,0777,true);
            chmod($dir,0777);
        }

        if(empty($dir_res)){
            trace('创建文件目录失败:'.json_encode($dir),'ERROR');
        }

        $info = $file->validate(['size'=>20971520,'ext'=>'xls,xlsx,csv'])->move($dir);

        if($info){
            $save_name = $info->getSaveName();
            return ['code'=>0,'msg'=>'文件上传成功','data'=>['save_path'=>$save_name,'path'=>config('common.excel_domain')]];
        }

        $error_info = $file->getError();
        return  ['code'=>1001,'msg'=>'','data'=>$error_info];
    }

    /**
     * 上传图片并返回二进制流
     * @return array
     */
    public function uploadPictureBinary($dir)
    {
        $file   = request()->file('file');
        if(empty($file)){
            return ['code'=>1001,'msg'=>'文件不存在','data'=>[]];
        }

        $dir = stripslashes($dir);

        if(!is_dir($dir)){
            $dir_res = mkdir($dir,0777,true);
            chmod($dir,0777);
        }

        if(empty($dir_res)){

            trace('创建图片目录失败:'.json_encode($dir).' ||| IP地址：'.$_SERVER['SERVER_ADDR'],'ERROR');
        }

        $info = $file->validate(['size'=>5242880,'ext'=>'jpg,png,gif,jpeg'])->move($dir);

        if($info){
            $save_name = $info->getSaveName();
            $pic_path = config('common.upload_protected_path').'/Upload/Picture/'.$save_name;
            return ['code'=>0,'msg'=>'图片上传成功','data'=>['save_path'=>$save_name,'data'=>$this->imgToBase64($pic_path)]];
        }

        $error_info = $file->getError();

        return  ['code'=>1001,'msg'=>'','data'=>$error_info];
    }
}