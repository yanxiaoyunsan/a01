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
defined('IN_ECJIA') or exit('No permission resources.');

/**
 * 单条收货地址信息
 * @author royalwang
 */
class info_module extends api_front implements api_interface {
    public function handleRequest(\Royalcms\Component\HttpKernel\Request $request) {	
    	
        //如果用户登录获取其session
        $this->authSession();
        $user_id = $_SESSION['user_id'];
    	if ($user_id <= 0) {
    		return new ecjia_error(100, 'Invalid session');
    	}
		$id = $this->requestData('address_id', 0);
		if(intval($id) < 1 || empty($user_id)){
			return new ecjia_error( 'invalid_parameter', RC_Lang::get ('system::system.invalid_parameter' ));
		}
		$location = $this->requestData('location', array());
		$seller_id = $this->requestData('seller_id');
		
		RC_Loader::load_app_func('admin_order', 'orders');

		$db_user_address = RC_Model::model('user/user_address_model');
		$db_region = RC_Model::model('shipping/region_model');
		$info = $db_user_address->find(array('address_id' => $id, 'user_id' => $user_id));

		/* 验证地址id */
		if (empty($info)) {
		    return new ecjia_error(13, '不存在的信息');
		}
		
		$consignee = get_consignee($user_id); // 取得默认地址
		
		$ids = array($info['country'], $info['province'], $info['city'], $info['district']);
		$ids = array_filter($ids);

		$data = $db_region->in(array('region_id' => implode(',', $ids)))->select();
		
		$out = array();
		foreach ($data as $key => $val) {
			$out[$val['region_id']] = $val['region_name'];
		}
		
		$local = true;
		if ($seller_id) {
            $local = RC_Api::api('user', 'neighbors_address_store', array('address' => $info, 'store_id' => $seller_id));
		}
		
		$result = array(
		    'id'         => $info['address_id'],
		    'consignee'  => $info['consignee'],
		    'email'      => $info['email'],
		    
		    'country'    => $info['country'],
		    'province'   => $info['province'],
		    'city'       => $info['city'],
		    'district'   => $info['district'],
		    'location'	 => array(
		        'longitude' => $info['longitude'],
		        'latitude'	=> $info['latitude'],
		    ),
		    
		    'country_name'   => isset($out[$info['country']]) ? $out[$info['country']] : '',
		    'province_name'  => isset($out[$info['province']]) ? $out[$info['province']] : '',
		    'city_name'      => isset($out[$info['city']]) ? $out[$info['city']] : '',
		    'district_name'  => isset($out[$info['district']]) ? $out[$info['district']] : '',
		    
		    'address'        => empty($info['address']) ? '' : $info['address'],
		    'address_info'   => empty($info['address_info']) ? '' : $info['address_info'],
		    'zipcode'        => empty($info['zipcode']) ? '' : $info['zipcode'],
		    'mobile'         => $info['mobile'],
		    'sign_building'  => empty($info['sign_building']) ? '' : $info['sign_building'],
		    'best_time'      => empty($info['best_time']) ? '' : $info['best_time'],
		    'default_address'=> $info['default_address'],
		    'tel'            => empty($info['tel']) ? '' : $info['tel'],
			'local'			 => $local ? 1 : 0,
		    
		    'default_address'=> $info['address_id'] == $consignee['address_id'] ? 1 :0,
		);
		
		return $result;		
	}
}

// end