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
		public static function apiDiscovery(string $get_version = ''): array
		{
			$discovery_api = config('discovery.api') ?: null;
			$resources_path = config('discovery.resources_path') ?: null;
			$registered_routes = \Route::getRoutes();

			if ($get_version) {
				$discovery_api = Arr::only($discovery_api, $get_version);
			}

			if (null == $discovery_api || null == $resources_path || null == $registered_routes) {
				return array();
			}

			foreach ($discovery_api as $version => $resources) {
				foreach ($resources as $controller_name => $data) {
					$controller = $resources_path . '\\' . $version . '\\' . $controller_name;
					self::$discovery[$controller_name] = self::_autoDiscovery($registered_routes, $controller, $data);
				}
			}

			return self::$discovery;
		}

		/**
		 * @param string $controller
		 * @param string $endpoint
		 *
		 * @return array|null
		 * @throws \ReflectionException
		 */
		private static function _autoDiscovery(array &$registered_routes, string $controller, $data): ?array
		{
			if (is_string($data))
				$data = array('endpoint' => $data);

			$controller_routes = self::_detectRoutesController($registered_routes, $controller, $data['endpoint']);

			$filtered = self::_detectControllerMethods($controller, $controller_routes);

			return $filtered;
		}

		private static function _detectRoutesController(array &$registered_routes, string $controller, string $endpoint): array
		{
			$detect_routes = array();
			foreach ($registered_routes as $key => $route) {
				if (strpos($key, $endpoint) !== false) {

					//Check if is nested API
					$controller_path = substr($route['action']['uses'], 0, strpos($route['action']['uses'], '@'));
					if ($controller !== $controller_path) {
						break;
					}

					//Get name of controller method from $route array
					$controller_method_name = substr($route['action']['uses'], strpos($route['action']['uses'], '@') + 1);

					//Create new element from method information
					$detect_routes[$controller_method_name] = array(
						'http' => $route['method'],
						'endpoint' => $route['uri'],
						'controller' => array(
							'path' => $controller_path,
							'method' => $controller_method_name,
						),
						'middleware' => array_key_exists('middleware', $route['action']) ? $route['action']['middleware'] : null,
					);
					unset($registered_routes[$key]);
				}
			}
			unset($controller_method_name);

			return $detect_routes;
		}

		private static function _detectControllerMethods(string $controller, array $controller_routes): ?array
		{
			$filtered = array();
			if (isset($controller::$PARAMETERS) && is_array($controller::$PARAMETERS) && !empty($controller::$PARAMETERS))
				$controller_params = $controller::$PARAMETERS;
			else
				$controller_params = array();

			$controller_methods = (new \ReflectionClass($controller))->getMethods(\ReflectionMethod::IS_PUBLIC);
			foreach ($controller_methods as $method) {
				//Check if the method is of the controller class and not of the parents
				if ($method->class !== $controller)
					break;

				//Check if method is registered in routes and is equal with controller method
				if (in_array($method->name, array_keys($controller_routes))) {
					$filtered[$method->name] = $controller_routes[$method->name];

					//Gets the method's parameters
					if ($controller_params && array_key_exists($method->name, $controller_params))
						$filtered[$method->name]['parameters'] = $controller_params[$method->name];

					unset($controller_routes[$method->name], $item);
				}
			}
			return $filtered;
		}
	}
