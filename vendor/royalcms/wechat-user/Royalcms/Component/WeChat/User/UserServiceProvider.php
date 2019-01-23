<?php

namespace Royalcms\Component\WeChat\User;

use Royalcms\Component\WeChat\User\Group;
use Royalcms\Component\WeChat\User\Tag;
use Royalcms\Component\WeChat\User\User;
use Royalcms\Component\Support\ServiceProvider;

/**
 * Class UserServiceProvider.
 */
class UserServiceProvider extends ServiceProvider
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $wechat A container instance
     */
    public function register()
    {
        $wechat = $this->royalcms['wechat'];
        
        $wechat['user'] = function ($wechat) {
            return new User($wechat['access_token']);
        };

        $group = function ($wechat) {
            return new Group($wechat['access_token']);
        };

        $tag = function ($wechat) {
            return new Tag($wechat['access_token']);
        };

        $wechat['user_group'] = $group;
        
        $wechat['user_tag'] = $tag;
    }
}
