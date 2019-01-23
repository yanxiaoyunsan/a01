<?php
/*
Name: 首页header模块
Description: 这是首页的header模块
*/
defined('IN_ECJIA') or header("HTTP/1.0 404 Not Found");exit('404 Not Found');
?>

<div class="ecjia-mod ecjia-header ecjia-header-index">
	<div class="ecjia-search-header">
		<span class="bg search-goods" data-url="{RC_Uri::url('touch/index/search')}{if $store_id}&store_id={$store_id}{/if}" {if $keywords neq ''}style="text-align: left;" data-val="{$keywords}"{/if}>
			<i class="iconfont icon-search"></i>{if $keywords neq ''}<span class="keywords">{$keywords}</span>{else}搜索{/if}
		</span>
	</div>
</div>
