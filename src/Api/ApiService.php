<?php

	namespace Kosmosx\Support\Api;

	use Kosmosx\Support\Api\Traits\Fractal;
	use Kosmosx\Helpers\Status\StatusService;
	use Illuminate\Support\Arr;
	use Illuminate\Http\Request;

	class ApiService extends StatusService
	{
		use Fractal;

		/**
		 * @param \Illuminate\Http\Request $request
		 * @param null|array|string        $get
		 *
		 * @return array
		 */
		public function query(Request $request, $get = null):array {
			if(null == $get || empty($get))
				return  $request->all();

			$params = array();

			if(is_string($get))
				$get = (array) $get;

			foreach ($get as $item)
				if(is_string($item) && $value = $request->get($item, null))
					$params[$item] = $value;

			return $params;
		}

		/**
		 * @param \Illuminate\Http\Request $request
		 * @param bool                     $onlyKeys
		 *
		 * @return array
		 */
		public function getInclude(Request $request, bool $onlyKeys = false): array {
			$query = $this->query($request, 'include');

			if (array_key_exists('include',$query))
				return $onlyKeys ? array_keys($query['include']) : $query['include'];

			return array();
		}

		/**
		 * @param \Illuminate\Http\Request $request
		 * @param bool                     $onlyKeys
		 *
		 * @return array
		 */
		public function getQuery(Request $request, bool $onlyKeys = false): array {
			$query = $this->query($request, 'query');

			if (array_key_exists('query',$query))
				return $onlyKeys ? array_keys($query['query']) : $query['query'];

			return array();
		}

		/**
		 * JSON:API standard v1.1
		 *
		 * @param array $resource
		 * @param array $includes
		 *
		 * @return array
		 */
		public function manipulateResource(array $resource, array $includes = array()) {
			$manipulate = array();

			if ($data = Arr::only($resource, ['type', 'id', 'attributes', 'relationships', 'links']))
				$manipulate['data'] = $data;

			if ($included = Arr::only($resource, $includes))
				$manipulate['included'] = $included;

			return $manipulate;
		}

		/**
		 * JSON:API standard v1.1
		 *
		 * @param array $resources
		 * @param array $includes
		 *
		 * @return array
		 */
		public function manipulateResources(array $resources, array $includes = array()) {
			$manipulate = array();

			foreach ($resources as $resource) {
				if ($data = Arr::only($resource, ['type', 'id', 'attributes', 'relationships', 'links']))
					$manipulate['data'][] = $data;

				if ($included = Arr::only($resource, $includes))
					$manipulate['included'][$resource['id']] = $included;
			}

			return $manipulate;
		}
	}