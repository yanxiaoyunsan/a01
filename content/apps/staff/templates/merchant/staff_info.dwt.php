<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia-merchant.dwt.php"} -->

<!-- {block name="footer"} -->
<script type="text/javascript">
	ecjia.merchant.staff_info.init();
</script>
<!-- {/block} -->

<!-- {block name="home-content"} -->
<style media="screen" type="text/css">
    label + div.col-lg-10{
        padding-top: 7px;
    }
</style>

<!-- {if $step eq 2} -->
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-info alert-dismissable">
			该员工手机号已通过审核，请尽快完善员工资料。
		</div>
    </div>
</div>
<!-- {/if} -->

<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header">
        <!-- {if $ur_here}{$ur_here}{/if} -->
        {if $action_link}
		<a class="btn btn-primary data-pjax" href="{$action_link.href}" id="sticky_a" style="float:right;margin-top:-3px;"><i class="fa fa-reply"></i> {$action_link.text}</a>
		{/if}
        </h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="bar">
	        <div class="step-bar">
	            <div class="step_{$step}"></div>
	        </div>
	        <ul class="step">
	            <li>
	                <span>1</span>
	                <p>验证手机号</p>
	            </li>
	            <li>
	                <span>2</span>
	                <p>完善资料</p>
	            </li>
	            <li>
	                <span>3</span>
	                <p>查看信息</p>
	            </li>
	        </ul>
	    </div>
        <section class="panel">
            <div class="panel-body">
                {if $step eq 1}
                  <form class="cmxform form-horizontal" name="theForm" action="{$form_action}"  method="post" >
                      <div class="form-group">
                          <label class="control-label col-lg-2">{t}员工手机号：{/t}</label>
                          <div class="col-lg-6">
                              <input class="form-control" name="mobile" id="mobile" placeholder="请输入手机号" type="text"/>
                          </div>
                         <input class="btn btn-primary" data-url="{url path='staff/merchant/get_code_value'}" id="get_code" type="button" value="获取短信验证码">
                      </div>

                      <div class="form-group">
                          <label class="control-label col-lg-2">{t}短信验证码：{/t}</label>
                          <div class="col-lg-6">
                              <input class="form-control" name="code" placeholder="请输入手机短信验证码" type="text"/>
                          </div>
                      </div>

                      <div class="form-group ">
                          <div class="col-lg-6 col-md-offset-2">
                             <input class="btn btn-primary" type="submit" value="下一步">
                          </div>
                      </div>
                  </form>
                 {/if} 
                        
                        
                {if $step eq 2}
                <form class="cmxform form-horizontal" name="theForm" action="{$form_action}"  method="post" enctype="multipart/form-data" >
                	<header class="panel-heading">员工基本信息 <hr></header>
                    <div class="form-group">
                        <label for="firstname" class="control-label col-lg-2">{lang key='staff::staff.staff_name_lable'}</label>
                        <div class="col-lg-6 controls">
                            <input class="form-control" id="name" name="name" type="text" />
                        </div>
                        <span class="input-must m_l7">{lang key='system::system.require_field'}</span>
                    </div>

                    <div class="form-group">
                        <label for="firstname" class="control-label col-lg-2">{lang key='staff::staff.staff_nick_name_lable'}</label>
                        <div class="col-lg-6">
                            <input class="form-control" id="nick_name" name="nick_name" type="text" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-lg-2">{lang key='staff::staff.staff_id_lable'}</label>
                       	<div class="col-lg-6 controls">
                            <input class="form-control" id="user_ident" name="user_ident" type="text"/>
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <label for="ccomment" class="control-label col-lg-2">{lang key='staff::staff.staff_intro_lable'}</label>
                        <div class="col-lg-6 controls">
                            <textarea class="form-control" id="introduction" name="introduction">{$staff.introduction}</textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="firstname" class="control-label col-lg-2">{lang key='staff::staff.staff_select_group_lable'}</label>
                        <div class="col-lg-6">
                            <select class="form-control" name="group_id">
                               <option value="0">{lang key='store::store.select_plz'}</option>
                               <!-- {html_options options=$group_list selected=$staff.group_id} -->
                            </select>
                        </div>
                    </div>

                    {if $manage_id eq 0}
  					<div class="form-group">
                        <label for="ccomment" class="control-label col-lg-2">备注：</label>
                        <div class="col-lg-6">
                            <textarea class="form-control" id="todolist" name="todolist"></textarea>
                             <p class="help-block">该备注仅店长可见</p>
                        </div>
                    </div>
                    {/if}
                      
                    <header class="panel-heading">员工账户信息 <hr></header>
                    <div class="form-group">
                        <label for="firstname" class="control-label col-lg-2">{lang key='staff::staff.staff_email_lable'}</label>
                        <div class="col-lg-6 controls">
                              <input class="form-control" id="email" name="email" type="email" />
                        </div>
                        <span class="input-must m_l7">{lang key='system::system.require_field'}</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="control-label col-lg-2">{lang key='staff::staff.staff_edit_password_lable'}</label>
                        <div class="col-lg-6 controls">
                              <input class="form-control" type="password" id="password" name="password"  />
                        </div>
                        <span class="input-must m_l7">{lang key='system::system.require_field'}</span>
                    </div>
                      
                    <div class="form-group">
                         <label for="confirm_password" class="control-label col-lg-2">{lang key='staff::staff.staff_confirm_password_lable'}</label>
                         <div class="col-lg-6 controls">
                              <input class="form-control" id="confirm_password" name="confirm_password" type="password" />
                          </div>
                        	<span class="input-must m_l7">{lang key='system::system.require_field'}</span>
                    </div>
                    
                    <div class="form-group ">
                        <div class="col-lg-6 col-md-offset-2">
                           <input class="btn btn-primary" type="submit" value="提交">
                        </div>
                    </div>
                </form>
                {/if} 
                       
                {if $step eq 3}
                <header class="panel-heading">员工基本信息 <hr></header>
                <div class="cmxform form-horizontal">
                  	<div class="form-group">
                        <label class="control-label col-lg-2">{t}员工名称：{/t}</label>
                        <div class="col-lg-10">
                            {$staff.name}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-lg-2">{t}员工昵称：{/t}</label>
                        <div class="col-lg-10">
                            {if $staff.nick_name}{$staff.nick_name}{else}{t}无{/t}{/if}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-lg-2">{t}员工编号：{/t}</label>
                        <div class="col-lg-10">
                         {if $staff.user_ident}{$staff.user_ident}{else}{t}无{/t}{/if}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-lg-2">{t}员工介绍：{/t}</label>
                        <div class="col-lg-10">
                            {$staff.introduction}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-lg-2">{t}所属员工组：{/t}</label>
                        <div class="col-lg-10">
                            {if $staff.group_name}{$staff.group_name}{else}{t}没有在任何组{/t}{/if}
                        </div>
                    </div>
                    
                    {if $manage_id eq 0}
                    <div class="form-group">
                      <label class="control-label col-lg-2">{t}员工备注：{/t}</label>
                      <div class="col-lg-10">
                           {if $staff.todolist}{$staff.todolist}<p class="help-block">该备注仅店长可见</p>{else}{t}无{/t}{/if}
                      </div>
                    </div>
                    {/if}
                    
                    <div class="form-group">
                        <label class="control-label col-lg-2">{t}加入时间：{/t}</label>
                        <div class="col-lg-10">
                            {$staff.add_time}
                        </div>
                    </div>
                    
                    <header class="panel-heading">员工账户信息 <hr></header>
                    <div class="form-group">
                        <label class="control-label col-lg-2">{t}手机账号：{/t}</label>
                        <div class="col-lg-10">
                            {$staff.mobile}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-lg-2">{t}邮件账号：{/t}</label>
                        <div class="col-lg-10">
                            {$staff.email}
                        </div>
                    </div>
                </div>
                {/if} 
            </div>
        </section>
    </div>
</div>
<!-- {/block} -->
