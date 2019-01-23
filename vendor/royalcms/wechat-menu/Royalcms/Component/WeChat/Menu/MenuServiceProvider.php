<?php 

namespace Royalcms\Component\WeChat\Menu;

use Royalcms\Component\Support\ServiceProvider;

/**
 * Class MenuServiceProvider.
 */
class MenuServiceProvider extends ServiceProvider
{
    /**
	 * Register the service provider.
	 *
	 * @return void
	 */
    public function register()
    {
        $wechat = $this->royalcms['wechat'];

        $wechat['menu'] = function () use ($wechat) {
            return new Menu($wechat['access_token']);
        };
    }
}
