<?php
/*
Name: 促销专场模块
Description: 这是首页的促销专场模块
*/
defined('IN_ECJIA') or header("HTTP/1.0 404 Not Found");exit('404 Not Found');
?>
{if $promotion_goods}
<div class="ecjia-mod ecjia-promotion-model ecjia-margin-t {if !$data && !$new_goods}ecjia-mod-pb35{/if}">
	<div class="separate">
		<div class="line"></div>
		<div class="line_middle">
			<div><p class="zhongwen">今日推荐</p></div>
			<div><p class="yingwen">DAY NEW</p></div>
		</div>
	</div>
	<div class="head-title">
		<h2><span class="neighbor"></span>精选推荐<a href="{$more_sales}" class="more_info">more</a></h2>
	</div>
	<div class="swiper-container swiper-promotion">
		<div class="swiper-wrapper">
			<!-- {foreach from=$promotion_goods item=val} 循环商品 -->
			<div class="swiper-slide">
				<a class="list-page-goods-img" href="{RC_Uri::url('goods/index/show')}&goods_id={$val.id}">
					<span class="goods-img">
                        <img src="{$val.img.small}" alt="{$val.name}">
                    </span>
					<span class="list-page-box">
						<span class="goods-name">{$val.name}</span>
						<span class="list-page-goods-price">
							<!--{if $val.promote_price}-->
							<span>{$val.promote_price}</span>
							<!--{else}-->
							<span>{$val.shop_price}</span>
							<!--{/if}-->
						</span>
					</span>
				</a>
				<img class="sales-icon" src="{$theme_url}images/icon-promote@2x.png">
			</div>
			<!-- {/foreach} -->
		</div>
	</div>
</div>
{/if}