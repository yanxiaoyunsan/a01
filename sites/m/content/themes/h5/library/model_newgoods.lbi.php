<?php
/*
Name: 新品推荐
Description: 这是首页的新品推荐模块
*/
defined('IN_ECJIA') or header("HTTP/1.0 404 Not Found");exit('404 Not Found');
?>
{if $new_goods}
<div class="ecjianew_top ecjia-mod ecjia-new-model ecjia-margin-t {if !$data}ecjia-mod-pb35{/if}">
	<div class="head-title ecjia-new-goods">
		<h2><span class="neighbor"></span>新品推荐<a href="{$more_news}" class="more_info">more</a></h2>
	</div>
	<div class="list">
		<div class="list_content">
			<!-- {foreach from=$new_goods item=val} 循环商品 -->
			<a href="{RC_Uri::url('goods/index/show')}&goods_id={$val.id}">
				<div class="list_box">
					<div class="list_box_img">
						<img src="{$val.img.small}" alt="{$val.name}">
					</div>
					<div class="list_box_title">
						<p>{$val.name}</p>
					</div>
					<div class="list_box_detail">
						<div class="list_box_name"><a></a></div>
						<div class="list_box_price"><span>{$val.shop_price}</span></div>
					</div>
				</div>
			</a>
			<!-- {/foreach} -->
		</div>
	</div>
</div>
{/if}
