<?php

	namespace Kosmosx\Support\Api;

	use Kosmosx\Support\Api\Traits\Fractal;
	use Illuminate\Support\Arr;

	class ApiService
	{
		use Fractal;
		static $discovery = array();
		static $orphan_routes = array();

		/**
		 * @param string $get_version
		 *
		 * @return array
		 * @throws \ReflectionException
		 */
		public static function apiDiscovery(?string $get_version = null, bool $whitOrphans = false): array
		{
			$discovery_api = config('discovery.resources') ?: null;
			$discovery_path = config('discovery.path') ?: null;
			$registered_routes = \Route::getRoutes();

			if (null == $get_version) {
				$discovery_api = Arr::only($discovery_api, $get_version);
			}

			if (null == $discovery_api || null == $registered_routes) {
				return array();
			}

			foreach ($discovery_api as $version => $resources) {
				foreach ($resources as $resource => $config) {
					if (!self::_isValidResources($config, $discovery_path))
						break;

					$resource_namespace = $config['path'] . '\\' . $version . '\\' . $resource;
					if (class_exists($resource_namespace)) {
						self::$discovery[$resource] = self::_autoDiscovery($registered_routes, $resource_namespace, $config['endpoint']);
					}
				}
			}

			$discovered = array('discovered' => self::$discovery);
			if ($whitOrphans)
				return array_merge($discovered, ['orphans' => self::$orphan_routes]);
			else
				return $discovered;
		}

		private static function _isValidResources(&$config, ?string $discovery_path = null): bool
		{
			if (is_array($config)) {
				if (!array_key_exists('endpoint', $config)) {
					return false;
				}

				if (!array_key_exists('path', $config)) {
					if (null == $discovery_path || !is_string($discovery_path))
						return false;
					else
						$config['path'] = $discovery_path;
				}

				return true;
			}

			if (is_string($config)) {
				if (null == $discovery_path || !is_string($discovery_path))
					return false;
				else
					$config = array('endpoint'=>$config, 'path' => $discovery_path);

				return true;
			}

			return false;
		}

		/**
		 * @param string $controller
		 * @param string $endpoint
		 *
		 * @return array|null
		 * @throws \ReflectionException
		 */
		private static function _autoDiscovery(array &$registered_routes, string $resource, $endpoint): ?array
		{
			$detected_routes = self::_detectRoutesController($registered_routes, $resource, $endpoint);

			$discovered = array();
			self::_detectControllerMethods($discovered, $resource, $detected_routes);

			return $discovered;
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

		private static function _detectControllerMethods(array &$discovered, string $controller, array $detected_routes): void
		{
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
				if (in_array($method->name, array_keys($detected_routes))) {
					$discovered[$method->name] = $detected_routes[$method->name];

					//Gets the api's parameters from controller
					if ($controller_params && array_key_exists($method->name, $controller_params))
						$discovered[$method->name]['parameters'] = $controller_params[$method->name];

					unset($detected_routes[$method->name], $item);
				}
			}

			if (!empty($detected_routes))
				self::$orphan_routes[] = $detected_routes;
		}
	}
