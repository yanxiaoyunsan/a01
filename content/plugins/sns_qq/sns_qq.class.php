<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//
/**
 * QQ登录
 */
defined('IN_ECJIA') or exit('No permission resources.');

use Ecjia\App\Connect\ConnectAbstract;
use Ecjia\App\Connect\ConnectUser;

class sns_qq extends ConnectAbstract
{
    protected $oauth;
    
    const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize";
    const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";
    const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";
    
    protected $recorder;
    public $urlUtils;
    
    /**
     * 获取插件代号
     *
     * @see \Ecjia\System\Plugin\PluginInterface::getCode()
     */
    public function getCode()
    {
        return $this->loadConfig('connect_code');
    }
    
    /**
     * 加载配置文件
     *
     * @see \Ecjia\System\Plugin\PluginInterface::loadConfig()
     */
    public function loadConfig($key = null, $default = null)
    {
        return $this->loadPluginData(RC_Plugin::plugin_dir_path(__FILE__) . 'config.php', $key, $default);
    }
    
    /**
     * 加载语言包
     *
     * @see \Ecjia\System\Plugin\PluginInterface::loadLanguage()
     */
    public function loadLanguage($key = null, $default = null)
    {
        $locale = RC_Config::get('system.locale');
    
        return $this->loadPluginData(RC_Plugin::plugin_dir_path(__FILE__) . '/languages/'.$locale.'/plugin.lang.php', $key, $default);
    }
    
    public function setConfig(array $config) 
    {
        parent::setConfig($config);
        
        $inc = array(
        	'appid'        => $this->config['sns_qq_appid'],
            'appkey'       => $this->config['sns_qq_appkey'],
            'callback'     => $this->callback_url(),
            'scope'        => 'get_user_info',
            'errorReport'  => true
        );
        $this->recorder = new Recorder($inc);
        $this->urlUtils = new UrlUtils();
    }
    
    /**
     * 生成授权网址
     */
    public function authorize_url() {
        $appid = $this->recorder->readInc("appid");
        $callback = $this->recorder->readInc("callback");
        $scope = $this->recorder->readInc("scope");
        
        //-------生成唯一随机串防CSRF攻击
        $state = md5(uniqid(rand(), TRUE));
        $this->recorder->write('state', $state);
        
        //-------构造请求参数列表
        $keysArr = array(
            "response_type"     => "code",
            "client_id"         => $appid,
            "redirect_uri"      => $callback,
            "state"             => $state,
            "scope"             => $scope,
        );
        
        $login_url = $this->urlUtils->combineURL(self::GET_AUTH_CODE_URL, $keysArr);
        
        return $login_url;
    }
    
    public function callback_url()
    {
        $redirect_uri = urlencode(RC_Uri::url('connect/callback/init', array('connect_code' => 'sns_qq')));
        return $redirect_uri;
    }
    
    /**
     * 登录成功后回调处理
     * @param $user_type 用户类型
     *          ConnectUser::USER,
     *          ConnectUser::MERCHANT,
     *          ConnectUser::ADMIN
     * @see \Ecjia\App\Connect\ConnectAbstract::callback()
     * @return \Ecjia\App\Connect\ConnectUser
     */
    public function callback($user_type = 'user') {
        $state = $this->recorder->read("state");
        $callback = $this->recorder->readInc("callback");

        //--------验证state防止CSRF攻击
        if($_GET['state'] != $state){
            return new ecjia_error('30001', ErrorCase::showError('30001'));
        }

        $token = $this->access_token($callback, $_GET['code']);
       
        $userinfo = $this->me();
        if (is_ecjia_error($userinfo)) {
            return $userinfo;
        }
        
        $connect_user = new ConnectUser($this->getCode(), $this->open_id, $user_type);
        $connect_user->saveOpenId($this->access_token, $this->refresh_token, serialize($userinfo), $this->expires_in);
        $connect_user->setUserName($userinfo['nickname']);
        
        if (intval($userinfo['ret']) === 0) {
            return $connect_user;
        } else {
            return new ecjia_error('sns_qq_authorize_failure', '登录授权失败，请换其他方式登录');
        }        
    }
    
    /**
     * 获取access token
     */
    public function access_token($callback_url, $code) {
        //-------请求参数列表
        $keysArr = array(
            "grant_type"    => "authorization_code",
            "client_id"     => $this->recorder->readInc("appid"),
            "redirect_uri"  => $this->recorder->readInc("callback"),
            "client_secret" => $this->recorder->readInc("appkey"),
            "code"          => $code,
        );
        //------构造请求access_token的url
        $token_url = $this->urlUtils->combineURL(self::GET_ACCESS_TOKEN_URL, $keysArr);
        $response = $this->urlUtils->get_contents($token_url);
        if (is_ecjia_error($response)) {
            return $response;
        }
        
        if (strpos($response, "callback") !== false) {
            $lpos       = strpos($response, "(");
            $rpos       = strrpos($response, ")");
            $response   = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg        = json_decode($response);
        
            if (isset($msg->error)) {
                return new ecjia_error($msg->error, $msg->error_description);
            }
        }
        
        $params = array();
        parse_str($response, $params);
        
        $this->recorder->write("access_token", $params["access_token"]);
        
        $this->access_token = $params["access_token"];
        $this->refresh_token = $params["refresh_token"];
        $this->expires_in = $params["expires_in"];

        return $params;
    }
    
    public function get_openid() {
        //-------请求参数列表
        $keysArr = array(
            "access_token" => $this->recorder->read("access_token")
        );
    
        $graph_url = $this->urlUtils->combineURL(self::GET_OPENID_URL, $keysArr);
        $response = $this->urlUtils->get_contents($graph_url);
    
        //--------检测错误是否发生
        if (strpos($response, "callback") !== false) {
            $lpos       = strpos($response, "(");
            $rpos       = strrpos($response, ")");
            $response   = substr($response, $lpos + 1, $rpos - $lpos -1);
        }
    
        $user = json_decode($response);
        if (isset($user->error)) {
            return new ecjia_error($user->error, $user->error_description);
        }
    
        //------记录openid
        $this->recorder->write("openid", $user->openid);
        $this->open_id = $user->openid;
        
        return $user->openid;
    }
    
    /**
     * 使用refresh token 获取新的access token
     * @param unknown $refresh_token
     */
    public function access_token_refresh($refresh_token) {
        
    }
    
    /**
     * 获取登录用户信息
     */
    public function me() {
        $open_id =  $this->get_openid();
        $this->oauth = new QQConnect($this->recorder, $this->urlUtils, $this->access_token, $open_id);
        $userinfo = $this->oauth->get_user_info();
        return $userinfo;
    }
    
    /**
     * 获取用户头像
     */
    public function get_headerimg() {
        return $this->profile['figureurl_qq_2'];
    }
    
    public function get_username() {
        return $this->profile['nickname'];
    }
    
}

// end