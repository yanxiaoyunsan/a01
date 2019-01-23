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
 * ECJIA 会员充值提现管理语言项
 */
return array(
	'edit' 			=> '编辑',
	'user_surplus' 	=> '预付款',
	'surplus_id' 	=> '编号',
	'user_id' 		=> '会员名称',
	'order_sn'		=> '订单编号',
	'surplus_amount'=> '金额',
	'add_date' 		=> '操作日期',
	'pay_mothed' 	=> '支付方式',
	'process_type' 	=> '类型',
	'confirm_date' 	=> '到款日期',
	'surplus_notic' => '管理员备注',
	'surplus_desc' 	=> '会员描述',
	'surplus_type' 	=> '操作类型',
	'no_user' 		=> '匿名购买',
		
	'surplus_type' => array(
		0 => '充值',
		1 => '提现',
	),
		
	'admin_user'	=> '操作员',
	'status' 		=> '到款状态',
	'confirm' 		=> '已完成',
	'unconfirm' 	=> '未确认',
	'cancel' 		=> '取消',
	'please_select' => '请选择...',
	'surplus_info' 	=> '会员金额信息',
	'check' 		=> '到款审核',
	
	'money_type' 		  	=> '币种',
	'surplus_add' 			=> '添加申请',
	'surplus_edit' 			=> '编辑申请',
	'attradd_succed' 		=> '您此次操作已成功！',
	'username_not_exist' 	=> '您输入的会员名称不存在！',
	'cancel_surplus' 		=> '您确定要取消这条记录吗?',
	'surplus_amount_error' 	=> '要提现的金额超过了此会员的帐户余额，此操作将不可进行！',
	'edit_surplus_notic' 	=> '现在的状态已经是 已完成，如果您要修改，请先将之设置为 未确认',
	'back_list' 			=> '返回充值和提现申请',
	'continue_add' 			=> '继续添加申请',
	'user_name_keyword' 	=> '请输入会员名称关键字',
	
	/* 提示信息  */
	'drop_success'			=> '删除成功！',
	'add_success' 			=> '添加成功！',
	'edit_success' 			=> '编辑成功！',
	
	/* JS语言项 */
	'js_languages' => array(
		'user_id_empty' 		=> '会员名称不能为空！',
		'deposit_amount_empty' 	=> '请输入充值的金额！',
		'pay_code_empty' 		=> '请选择支付方式',
		'deposit_amount_error'	=> '请按正确的格式输入充值的金额！',
		'deposit_type_empty' 	=> '请填写类型！',
		'deposit_notic_empty' 	=> '请填写管理员备注！',
		'deposit_desc_empty' 	=> '请填写会员描述！',
	),
	
	'recharge_withdrawal_apply' 		=> '充值提现申请',
	'recharge_apply' 					=> '充值申请',
	'withdrawal_apply' 					=> '提现申请',
	'log_username' 						=> '会员名称是',
	'batch_deletes_ok' 					=> '批量删除成功',
	'update_recharge_withdrawal_apply' 	=> '更新充值提现申请',
	'bulk_operations'					=> '批量操作',
	'application_confirm'				=> '已完成的申请无法被删除，你确定要删除选中的列表吗？',
	'select_operated_confirm'			=> '请选中要操作的项',
	'batch_deletes' 					=> '批量删除',
	'to' 								=> '至',
	'filter'							=> '筛选',
	'start_date' 						=> '开始日期',
	'end_date' 							=> '结束日期',
	'delete'							=> '删除',
	'delete_surplus_confirm'			=> '您确定要删除充值提现记录吗？',
	'user_information'					=> '会员信息',
	'anonymous_member' 					=> '匿名会员',
	'yuan'								=> '元',
	'deposit'							=> '充值',
	'withdraw'							=> '提现',
	'edit_remark'						=> '编辑备注',
	
	'label_user_id' 			=> '会员名称：',
	'label_surplus_amount'		=> '金额：',
	'label_pay_mothed' 			=> '支付方式：',
	'label_process_type' 		=> '类型：',
	'label_surplus_notic' 		=> '管理员备注：',
	'label_surplus_desc' 		=> '会员描述：',
	'label_status' 				=> '到款状态：',
	'submit_update'				=> '更新',
	
	'keywords_required'			=> '请输入关键字',
	'username_required'			=> '请输入会员名称',
	'amount_required'			=> '请输入金额',
	'check_time'				=> '开始时间不得大于结束时间！',
	
	'user_name_is'				=> '会员名称是%s，',
	'money_is'					=> '金额是%s',
	'delete_record_count'		=> '本次删除了 %s 条记录',
	'select_operate_item'		=> '请先选择需要操作的项',
	'withdraw_apply'			=> '提现申请',
	'pay_apply'					=> '充值申请',
	'recharge_order'			=> '充值订单',
	'withdraw_apply'			=> '提现申请',
	'min_amount_error'			=> '充值或提现金额最低一元起',
	'user_mobile'				=> '会员手机号码',
	'back_recharge_list'		=> '返回充值订单',
	'back_withdraw_list'		=> '返回提现申请',
	'user_keyword'				=> '请输入会员手机号或名称等关键字'
	
);

// end