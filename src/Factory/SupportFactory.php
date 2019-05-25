<?php
	/**
	 * Created by PhpStorm.
	 * User: fabrizio
	 * Date: 27/12/18
	 * Time: 13.32
	 */

	namespace Kosmosx\Support\Factory;

	use Kosmosx\Support\Status\StatusService;
	use Kosmosx\Support\Api\ApiService;

	class SupportFactory implements SupportFactoryInteface
	{
		/**
		 *    Helper function to wrap ServiceStatus return.
		 *
		 * @param int $statusCode
		 *    The status code to be passed to ServiceStatus.
		 * @param        array
		 *    Custom data.
		 * @param string $message
		 *    A message.
		 *
		 * @return StatusService
		 */
		public static function fail(?int $statusCode = null, array $data = array(), string $message = null): StatusService {
			return StatusService::set(false, $statusCode, $data, $message);
		}

		/**
		 *    Helper function to wrap StatusService return.
		 *
		 * @param array         $data
		 *    The data this service is returing.
		 * @param null          $statusCode
		 * @param string|string $message
		 *    A message.
		 *
		 * @return ServiceStatus
		 */
		public static function success(?int $statusCode = null, array $data = array(), string $message = null): StatusService {
			return StatusService::set(true, $statusCode, $data, $message);
		}

		/**
		 * @return ApiService
		 */
		public function api(): ApiService {
			return new ApiService();
		}
	}