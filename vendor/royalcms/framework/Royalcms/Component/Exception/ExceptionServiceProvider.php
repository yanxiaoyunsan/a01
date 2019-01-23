<?php namespace Royalcms\Component\Exception;

use Royalcms\Component\Whoops\Run;
use Royalcms\Component\Whoops\Handler\PrettyPageHandler;
use Royalcms\Component\Whoops\Handler\JsonResponseHandler;
use Royalcms\Component\Support\ServiceProvider;

class ExceptionServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerDisplayers();

		$this->registerHandler();
	}

	/**
	 * Register the exception displayers.
	 *
	 * @return void
	 */
	protected function registerDisplayers()
	{
		$this->registerPlainDisplayer();

		$this->registerDebugDisplayer();
	}

	/**
	 * Register the exception handler instance.
	 *
	 * @return void
	 */
	protected function registerHandler()
	{
		$this->royalcms['exception'] = $this->royalcms->share(function($royalcms)
		{
			return new Handler($royalcms, $royalcms['exception.plain'], $royalcms['exception.debug']);
		});
	}

	/**
	 * Register the plain exception displayer.
	 *
	 * @return void
	 */
	protected function registerPlainDisplayer()
	{
		$this->royalcms['exception.plain'] = $this->royalcms->share(function($royalcms)
		{
			// If the application is running in a console environment, we will just always
			// use the debug handler as there is no point in the console ever returning
			// out HTML. This debug handler always returns JSON from the console env.
			if ($royalcms->runningInConsole())
			{
				return $royalcms['exception.debug'];
			}
			else
			{
				return new PlainDisplayer;
			}
		});
	}

	/**
	 * Register the Whoops exception displayer.
	 *
	 * @return void
	 */
	protected function registerDebugDisplayer()
	{
		$this->registerWhoops();

		$this->royalcms['exception.debug'] = $this->royalcms->share(function($royalcms)
		{
			return new WhoopsDisplayer($royalcms['whoops'], $royalcms->runningInConsole());
		});
	}

	/**
	 * Register the Whoops error display service.
	 *
	 * @return void
	 */
	protected function registerWhoops()
	{
		$this->registerWhoopsHandler();

		$this->royalcms['whoops'] = $this->royalcms->share(function($royalcms)
		{
			// We will instruct Whoops to not exit after it displays the exception as it
			// will otherwise run out before we can do anything else. We just want to
			// let the framework go ahead and finish a request on this end instead.
			with($whoops = new Run)->allowQuit(false);

			$whoops->writeToOutput(false);

			return $whoops->pushHandler($royalcms['whoops.handler']);
		});
	}

	/**
	 * Register the Whoops handler for the request.
	 *
	 * @return void
	 */
	protected function registerWhoopsHandler()
	{
		if ($this->shouldReturnJson())
		{
			$this->royalcms['whoops.handler'] = $this->royalcms->share(function()
			{
					return new JsonResponseHandler;
			});
		}
		else
		{
			$this->registerPrettyWhoopsHandler();
		}
	}

	/**
	 * Determine if the error provider should return JSON.
	 *
	 * @return bool
	 */
	protected function shouldReturnJson()
	{
		return $this->royalcms->runningInConsole() || $this->requestWantsJson();
	}

	/**
	 * Determine if the request warrants a JSON response.
	 *
	 * @return bool
	 */
	protected function requestWantsJson()
	{
		return $this->royalcms['request']->ajax() || $this->royalcms['request']->wantsJson();
	}

	/**
	 * Register the "pretty" Whoops handler.
	 *
	 * @return void
	 */
	protected function registerPrettyWhoopsHandler()
	{
		$me = $this;

		$this->royalcms['whoops.handler'] = $this->royalcms->share(function() use ($me)
		{
			with($handler = new PrettyPageHandler)->setEditor('sublime');

			// If the resource path exists, we will register the resource path with Whoops
			// so our custom Laravel branded exception pages will be used when they are
			// displayed back to the developer. Otherwise, the default pages are run.
			if ( ! is_null($path = $me->resourcePath()))
			{
				$handler->setResourcesPath($path);
			}

			return $handler;
		});
	}

	/**
	 * Get the resource path for Whoops.
	 *
	 * @return string
	 */
	public function resourcePath()
	{
		if (is_dir($path = $this->getResourcePath())) return $path;
	}

	/**
	 * Get the Whoops custom resource path.
	 *
	 * @return string
	 */
	protected function getResourcePath()
	{
		return __DIR__.'/resources';
	}

}
