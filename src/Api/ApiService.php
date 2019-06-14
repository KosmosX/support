<?php

	namespace Kosmosx\Support\Api;

	use Kosmosx\Support\Api\Traits\Fractal;
	use Kosmosx\Helpers\Status\StatusService;
	use Illuminate\Support\Arr;
	use Illuminate\Http\Request;

	/**
	 * Class ApiService
	 * @package Kosmosx\Support\Api
	 */
	class ApiService extends StatusService
	{
		use Fractal;

		/**
		 * @param \Illuminate\Http\Request $request
		 * @param bool                     $onlyKeys
		 *
		 * @return array
		 */
		public function getInclude(Request $request, bool $onlyKeys = false): array {
			$query = $this->query($request, 'include');

			if (array_key_exists('include', $query))
				return $onlyKeys ? array_keys($query['include']) : $query['include'];

			return array();
		}

		/**
		 * @param \Illuminate\Http\Request $request
		 * @param null|array|string        $get
		 *
		 * @return array
		 */
		protected function query(Request $request, $get = null): array {
			if (null == $get || empty($get))
				return $request->all();

			$params = array();

			if (is_string($get))
				$get = (array)$get;

			foreach ($get as $item)
				if (is_string($item) && $value = $request->get($item, null))
					$params[$item] = $value;

			return $params;
		}

		/**
		 * @param \Illuminate\Http\Request $request
		 * @param bool                     $onlyKeys
		 *
		 * @return array
		 */
		public function getQuery(Request $request, bool $onlyKeys = false): array {
			$query = $this->query($request, 'query');

			if (array_key_exists('query', $query))
				return $onlyKeys ? array_keys($query['query']) : $query['query'];

			return array();
		}

		/**
		 * Alias query method
		 *
		 * @param \Illuminate\Http\Request $request
		 *
		 * @return array
		 */
		public function getAll(Request $request) {
			return $this->query($request);
		}

		/**
		 * Parsa le informazioni passate nel body della richiesta
		 * con lo standard JSON:API v1.1
		 *
		 * @param $inputs
		 *
		 * @return array
		 */
		public function parseJsonApiRequestBody(Request $request) {
			$data = array();
			$inputs = $request->all();

			if(!array_key_exists('data', $inputs))
				return $data;

			$inputs = $inputs['data'];
			foreach ($inputs as $key => $input) {
				if ($key === 'attributes') {
					foreach ($input as $itemKey => $item)
						$data[$itemKey] = $item;
				}
				if ($key === 'relationships') {
					foreach ($input as $itemKey => $item)
						$data[$itemKey . '_id'] = $item['id'];
				}
			}

			return $data;
		}
	}