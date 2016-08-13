<?php

namespace MolnApps\Repository\Traits;

use \MolnApps\Repository\Contracts\Model;
use \MolnApps\Repository\Contracts\Middleware;

trait ModelMiddlewares
{
	private $middlewares = [];

	public function addMiddleware($name, Middleware $middleware)
	{
		$this->middlewares[$name] = $middleware;

		return $this;
	}

	public function removeMiddleware($name)
	{
		unset($this->middlewares[$name]);

		return $this;
	}

	public function hasMiddleware($name)
	{
		return isset($this->middlewares[$name]);
	}

	protected function authorize(Model $model, $operation)
	{
		foreach ($this->middlewares as $middleware) {
			if ( ! $middleware->authorize($model, $operation)) {
				return false;
			}
		}

		return true;
	}
}