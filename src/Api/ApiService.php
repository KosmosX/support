<?php

	namespace Kosmosx\Support\Api;

	use Kosmosx\Support\Api\Traits\Fractal;
	use Illuminate\Support\Arr;

	class ApiService
	{
		use Fractal;

		static $discovery = array();

		/**
		 * @param string $get_version
		 *
		 * @return array
		 * @throws \ReflectionException
		 */
		public static function discovery(?string $endpoint = null, array $disable = array()): array
		{
			self::$discovery = array();

			$routes = \Route::getRoutes();

			self::_autoDiscovery($routes, $endpoint, $disable);

			return self::$discovery;
		}

		private static function _autoDiscovery(array &$routes, ?string $endpoint = null, array $disable): void
		{
			foreach ($routes as $key => $route) {
				if (array_key_exists('uses', $route['action'])) {
					$namespace = substr($route['action']['uses'], 0, strpos($route['action']['uses'], '@'));

					$resource_name = substr($namespace, strrpos($namespace, '\\') + 1);

					if (!class_exists($namespace))
						continue;

					if (self::_disableResource($resource_name, $disable))
						continue;

					if (null != $endpoint && false === strpos($route['uri'], $endpoint))
						continue;

					$reflaction_obj = new \ReflectionClass($namespace);

					//Get method's name
					$method = substr($route['action']['uses'], strpos($route['action']['uses'], '@') + 1);

					//Create resource array
					$resource = array(
						'http' => $route['method'],
						'endpoint' => $route['uri'],
						'middleware' => array_key_exists('middleware', $route['action']) ? $route['action']['middleware'] : null,
						'controller' => array(
							'namespace' => $namespace,
							'extend' => $reflaction_obj->getParentClass(),
							'implements' => $reflaction_obj->getInterfaces(),
							'name' => $resource_name,
							'method' => $method,
						),
					);

					if (self::_isRegistrable($method, $reflaction_obj)) {
						if (isset($namespace::$PARAMETERS) && is_array($namespace::$PARAMETERS) && array_key_exists($method, $namespace::$PARAMETERS))
							$resource['parameters'] = $namespace::$PARAMETERS[$method];
						if (null != $endpoint)
							self::$discovery[$endpoint][$resource_name][$method] = $resource;
						else
							self::$discovery[$reflaction_obj->getNamespaceName()][$resource_name][$method] = $resource;
					}
				}
				unset($routes[$key]);
			}
		}

		private static function _disableResource(string $resource, array $disable = array()): bool
		{
			foreach ($disable as $key => $exclude)
				if ($resource === $exclude)
					return true;

			return false;
		}

		private static function _isRegistrable(string $method, $reflaction_obj): bool
		{
			$controller_methods = $reflaction_obj->getMethods(\ReflectionMethod::IS_PUBLIC);

			foreach ($controller_methods as $controller_method) {
				//Check if the method is of the controller class and not of the parents
				if ($controller_method->class !== $reflaction_obj->getName())
					break;

				//Check if method is registered in routes and is equal with controller method
				if ($controller_method->name === $method)
					return true;
			}

			return false;
		}
	}
