<?php namespace Royalcms\Component\HttpKernel;

use Royalcms\Component\Support\MessageBag;
use Symfony\Component\HttpFoundation\Cookie;
use Royalcms\Component\Session\Store as SessionStore;
use Royalcms\Component\Support\Contracts\MessageProviderInterface;

class RedirectResponse extends \Symfony\Component\HttpFoundation\RedirectResponse {

	/**
	 * The request instance.
	 *
	 * @var \Royalcms\Component\HttpKernel\Request
	 */
	protected $request;

	/**
	 * The session store implementation.
	 *
	 * @var \Royalcms\Component\Session\Store
	 */
	protected $session;

	/**
	 * Set a header on the Response.
	 *
	 * @param  string  $key
	 * @param  string  $value
	 * @param  bool  $replace
	 * @return \Royalcms\Component\HttpKernel\RedirectResponse
	 */
	public function header($key, $value, $replace = true)
	{
		$this->headers->set($key, $value, $replace);

		return $this;
	}

	/**
	 * Flash a piece of data to the session.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return \Royalcms\Component\HttpKernel\RedirectResponse
	 */
	public function with($key, $value = null)
	{
		if (is_array($key))
		{
			foreach ($key as $k => $v) $this->with($k, $v);
		}
		else
		{
			$this->session->flash($key, $value);
		}

		return $this;
	}

	/**
	 * Add a cookie to the response.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Cookie  $cookie
	 * @return \Royalcms\Component\HttpKernel\RedirectResponse
	 */
	public function withCookie(Cookie $cookie)
	{
		$this->headers->setCookie($cookie);

		return $this;
	}

	/**
	 * Flash an array of input to the session.
	 *
	 * @param  array  $input
	 * @return \Royalcms\Component\HttpKernel\RedirectResponse
	 */
	public function withInput(array $input = null)
	{
		$input = $input ?: $this->request->input();

		$this->session->flashInput($input);

		return $this;
	}

	/**
	 * Flash an array of input to the session.
	 *
	 * @param  dynamic  string
	 * @return \Royalcms\Component\HttpKernel\RedirectResponse
	 */
	public function onlyInput()
	{
		return $this->withInput($this->request->only(func_get_args()));
	}

	/**
	 * Flash an array of input to the session.
	 *
	 * @param  dynamic  string
	 * @return \Royalcms\Component\HttpKernel\RedirectResponse
	 */
	public function exceptInput()
	{
		return $this->withInput($this->request->except(func_get_args()));
	}

	/**
	 * Flash a container of errors to the session.
	 *
	 * @param  \Royalcms\Component\Support\Contracts\MessageProviderInterface|array  $provider
	 * @return \Royalcms\Component\HttpKernel\RedirectResponse
	 */
	public function withErrors($provider)
	{
		if ($provider instanceof MessageProviderInterface)
		{
			$this->with('errors', $provider->getMessageBag());
		}
		else
		{
			$this->with('errors', new MessageBag((array) $provider));
		}

		return $this;
	}

	/**
	 * Get the request instance.
	 *
	 * @return  \Royalcms\Component\HttpKernel\Request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Set the request instance.
	 *
	 * @param  \Royalcms\Component\HttpKernel\Request  $request
	 * @return void
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * Get the session store implementation.
	 *
	 * @return \Royalcms\Component\Session\Store
	 */
	public function getSession()
	{
		return $this->session;
	}

	/**
	 * Set the session store implementation.
	 *
	 * @param  \Royalcms\Component\Session\Store  $store
	 * @return void
	 */
	public function setSession(SessionStore $session)
	{
		$this->session = $session;
	}

	/**
	 * Dynamically bind flash data in the session.
	 *
	 * @param  string  $method
	 * @param  array  $parameters
	 * @return void
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call($method, $parameters)
	{
		if (starts_with($method, 'with'))
		{
			return $this->with(snake_case(substr($method, 4)), $parameters[0]);
		}

		throw new \BadMethodCallException("Method [$method] does not exist on Redirect.");
	}

}
