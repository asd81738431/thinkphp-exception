<?php
namespace app\common\facade;

use think\Facade;

/**
 * @see \app\common\lib\Helper
 * @package think\facade
 * @mixin \app\common\lib\Helper
 * @method static string getRandChar(int $length) 生成指定位数的随机字符串
 * @method static array week_of_month(string $time) 格式化时间
 * @method static string createToken() 生成token令牌
 * @method static string createUserToken(int $userid,int $login_type = 3,int $openid = 0,int $unionid = 0,string $session_key = '',int $time=43200) 创建用户Token(适用于小程序)
 * @method static true downloadFile(string $url, string $absolute_path = '') 下载文件
 * @method static string getWid(string $wid = '') 获取微信用户id
 * @method static false|string downWechatPhoto(string $url) 下载微信头像
 * @method static array|string getHideIdCard(string $idCard) 身份证神秘截取
 * @method static array|string getHideMobile(string $mobile) 手机号神秘截取
 * @method static array getIdCardInfo(string $idcard) 获取身份证信息（一代与二代）获取生日、年龄、性别
 * @method static array passwordAddSalt(string $password) 密码加盐
 * @method static array getTree(array $list,string $pk = 'id', string $pid = 'parentid',string $child = 'child',int $root = 0) 地区的查找
 * @method static array _setArrayFormat(array $data) 改变配置文件数据格式
 * @method static array sendcode(string $mobile,int $messType=2) 发送验证码
 * @method static false|array curl_get(string $url) 模拟表单提交(get)
 * @method static string|bool curl_post(string $url,array $data=[]) 模拟表单提交(post)
 * @method static string|array getCommonConfig(string $name) 获取配置
 * @method static int getphonetype(string $phone) 手机服务商函数
 * @method static int getActivityStatus(string $sign_start_time,string $sign_end_time,string $start_time,string $end_time) 根据时间获取活动状态
 * @method static null funDebug(string $name,string $other) 接口日志
 * @method static string encrypt(int $id,string $key='!Qw89Kl$d.kd^@*)') openssl加密
 * @method static string decrypt(string $encrypt,string $key='!Qw89Kl$d.kd^@*)') openssl解密
 * @method static string openssl_str(string $str) openssl_str
 * @method static string encrypt2(int $id,string $key='*)9Kl$d.!Qw8kd^@', string $iv='%)^=(!KjW$kjm983',int $level = 1) openssl加密（新版）
 * @method static int decrypt2(string $encrypt,string $key='*)9Kl$d.!Qw8kd^@', string $iv='%)^=(!KjW$kjm983',string $level = 1) openssl解密（新版）
 * @method static bool updateInstructorUserId(string $idcard,string $userid) 用户注册并匹配上身份证，则修改辅导员表用户ID
 * @method static string assemble(array $params) assemble
 * @method static array|mixed|string pushLeagueLog(array $arr = [],string $time = '',string $content = '') 添加关系转接内容数组
 * @method static int|mixed getDbStatistics(string $time,string $type) 获取缓存数据库中的统计
 * @method static boolean redisLock(string $sign,int $expire = 1,bool $isMsec = false,bool $isPrepend = true,string $prefix = 'lock_') 锁住某个操作
 * @method static boolean redisDelLock(string $key) 删除redis锁
 * @method static string imgToBase64(string $img_file) 图片输出为base64二进制流
 * @method static string create_uuid(string $prefix = "") 创建uuid
 * @method static string substr_cut(string $user_name) 截取字符串
 * @method static float get_two_point_distance(float $lat1,float $lat2,float $lng1,float $lng2) 经纬度算距离
 * @method static array uploadPicture() 上传并验证图片到服务器
 * @method static array uploadExcel() 上传并验证Excel到服务器
 * @method static array uploadPictureBinary(string $dir) 上传图片并返回二进制流
 *
 */
class HelperFacade extends Facade
{
    protected static function getFacadeClass()
    {
        return 'app\common\lib\Helper';
    }
}