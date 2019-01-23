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
 * ECJIA 管理中心模版管理程序
 */
defined('IN_ECJIA') or exit('No permission resources.');

class admin_library extends ecjia_admin {

	private $theme;

	public function __construct() {
		parent::__construct();

		$this->db_template    = RC_Loader::load_model('template_model');
		$this->db_plugins     = RC_Loader::load_model('plugins_model');
		$this->db_shop        = RC_Loader::load_model('shop_config_model');
		$this->theme          = Ecjia_ThemeManager::driver();

		RC_Style::enqueue_style('chosen');
		RC_Style::enqueue_style('uniform-aristo');
		RC_Script::enqueue_script('jquery-chosen');
		RC_Script::enqueue_script('smoke');
		RC_Script::enqueue_script('ecjia-utils');
		RC_Script::enqueue_script('jquery-uniform');
		RC_Script::enqueue_script('jquery-form');
		
		RC_Script::enqueue_script('acejs', RC_Uri::admin_url('statics/lib/acejs/ace.js'), array(), false, true);
		RC_Script::enqueue_script('acejs-emmet', RC_Uri::admin_url('statics/lib/acejs/ext-emmet.js'), array(), false, true);
		
		RC_Script::enqueue_script('template', RC_App::apps_url('statics/js/template.js', __FILE__));

		$admin_template_lang = array(
				'editlibrary'       	=> __('您确定要保存编辑内容吗？'),
				'choosetemplate'    	=> __('使用这个模板'),
				'choosetemplateFG'  	=> __('使用这个模板风格'),
				'abandon'           	=> __('您确定要放弃本次修改吗？'),
				'write'             	=> __('请先输入内容！'),
				'ok'                	=> __('确定'),
				'cancel'            	=> __('取消'),
				'confirm_leave'			=> __('您的修改内容还没有保存，您确定离开吗？'),
				'confirm_leave'			=> __('连接错误，请重新选择!'),
				'confirm_edit_project'	=> __('修改库项目是危险的高级操作，修改错误可能会导致前台无法正常显示。您依然确定要修改库项目吗？')
		);
		
		RC_Script::localize_script('template', 'admin_template_lang', $admin_template_lang);
		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('外观'), RC_Uri::url('theme/admin_template/init')));
	}

	/**
	 * 管理库项目
	 */
	public function init() {
		$this->admin_priv('library_manage');

        $full = isset($_GET['full']) && !empty($_GET['full']) ? 1 : 0;
        $lib = isset($_GET['lib']) ? $_GET['lib'] : '';
        $lib = str_replace(array('.lbi.php', '.php', '.lbi'), '', $lib);

		$libraries = $this->theme->getAllLibraryFiles();
		
        if (empty($lib) && !empty($libraries) && is_array($libraries)) {
            $lib = key($libraries);
        }

        $library = new Ecjia\System\Theme\ThemeLibrary($this->theme, trim($lib) . '.lbi.php');
		$library_info = $library->loadLibrary();

        $library_info['file'] = '';
        $library_info['name'] = '';
        if (isset($libraries[$lib])) {
            $library_info['file'] = $libraries[$lib]['File'];
            $library_info['name'] = $libraries[$lib]['File'].' - '.$libraries[$lib]['Name'];
            $libraries[$lib]['choose'] = 1;
        }

        $is_writable        = royalcms('files')->isWritable($library->getFilePath());
        $library_file       = str_replace(SITE_ROOT, '', $library_file);

        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('库项目管理')));
        $this->assign('ur_here'         , __('库项目管理'));
        $this->assign('lib'             , $lib);
        $this->assign('libraries'       , $libraries);
        $this->assign('full'            , $full);// 是否全屏
        $this->assign('is_writable'     , $is_writable);// library能否写入
        $this->assign('library_dir'     , $library_file);// library目录地址

        $this->assign('library_name'    , $library_info['name']);
        $this->assign('library_html'    , json_encode($library_info['html']));
        
        $this->assign('form_action', RC_Uri::url('theme/admin_library/update_library'));

		$this->display('template_library.dwt');
	}

	/**
	 * 更新库项目内容
	 */
	public function update_library() {
		$this->admin_priv('library_manage');

		$html = stripslashes($_POST['html']);

		$library = new Ecjia\System\Theme\ThemeLibrary($this->theme, trim($_POST['lib']) . '.lbi.php');
		
		if ($library->updateLibrary($html)) {
			return $this->showmessage(__('库项目内容已经更新成功。'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
		} else {
            $templates          = $this->theme->get_theme_info();
            $libraries          = $this->theme->get_libraries();
            $library_file_name  = isset($libraries[$_POST['lib']]['File']) ? $libraries[$_POST['lib']]['File'] : '';
            $library_file       = str_replace(SITE_ROOT, '', SITE_THEME_PATH) . $templates['code'] . DS . 'library' . DS . $library_file_name;
			return $this->showmessage(sprintf(__('编辑库项目内容失败。请检查 %s 目录是否可以写入。'), $library_file), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
	}

}

// end
