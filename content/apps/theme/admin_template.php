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

class admin_template extends ecjia_admin {

    /**
     * 当前主题对象
     *
     * @var \Ecjia\System\Theme\Theme;
     */
	private $theme;

	public function __construct() {
		parent::__construct();

		$this->theme = Ecjia_ThemeManager::driver();

		RC_Style::enqueue_style('chosen');
		RC_Style::enqueue_style('uniform-aristo');
		RC_Script::enqueue_script('jquery-chosen');
		RC_Script::enqueue_script('smoke');
		RC_Script::enqueue_script('jquery-uniform');

		
		RC_Script::enqueue_script('template', RC_App::apps_url('statics/js/template.js', __FILE__));		
		$admin_template_lang = array(
				'choosetemplate'     => __("启用新的模板将覆盖原来的模板。\n您确定要启用选定的模板吗？"),
				'choosetemplateFG'   => __('您确定要启用选定的模板风格吗？'),
				'abandon'            => __('您确定要放弃本次修改吗？'),
				'write'              => __('请先输入内容！'),
				'ok'                 => __('确定'),
				'cancel'             => __('取消')
		);
		RC_Script::localize_script('template', 'admin_template_lang', $admin_template_lang );
		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('外观'), RC_Uri::url('theme/admin_template/init')));
	}

	/**
	 * 模版列表
	 */
	public function init() {
		$this->admin_priv('template_select');

		/* 获得当前的模版的信息 */
		$curr_style = Ecjia_ThemeManager::getStyleName(); 
		$template = $this->theme->loadSpecifyStyle($curr_style)->process();
		$template_styles = $this->theme->getThemeStyles();
		
		/* 获得可用的模版 */
		$availables = Ecjia_ThemeManager::getAvailableThemes();

		/* 获得可用的模版的可选风格数组 */
		$available_templates = array();
		if (count($availables) > 0) {
			foreach ($availables as $key => $theme) {
			    if ($key == $this->theme->getThemeCode()) {
			        continue;
			    }
				$templates_style[$key] = $theme->getThemeStyles();
				$available_templates[$key] = $theme->getDefaultStyle()->process();
			}
		}

		ecjia_screen::get_current_screen()->remove_last_nav_here();
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here('主题管理'));
		
		$this->assign('ur_here',                  '主题管理');
		$this->assign('curr_tpl_style',           $curr_style);
		$this->assign('template_style',           $templates_style);
		$this->assign('curr_template',            $template);
		$this->assign('available_templates',      $available_templates);
		$this->assign('available_templates_count',  count($available_templates));
		$this->assign('curr_template_styles',     $template_styles);

		$this->display('template_list.dwt');
	}

	/**
	 * 安装模版
	 */
	public function install() {
		$this->admin_priv('template_select');
		
		$tpl_name = trim($_GET['tpl_name']);
		$tpl_fg   = trim($_GET['tpl_fg']) ?: ''; 
		if ($tpl_name != Ecjia_ThemeManager::getTemplateName()) {
		    ecjia_config::write(Ecjia_ThemeManager::getTemplateCode(), $tpl_name);
		}
		
        if ($tpl_fg != Ecjia_ThemeManager::getStyleName()) {
            ecjia_config::write(Ecjia_ThemeManager::getStyleCode(), $tpl_fg);
        }

		return $this->showmessage('启用模板成功。', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('theme/admin_template/init')));
	}

}

// end
