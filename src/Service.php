<?php
	/**
	 * Created by PhpStorm.
	 * User: fabrizio
	 * Date: 27/12/18
	 * Time: 13.32
	 */

	namespace Kosmosx\Support;

	use Kosmosx\Support\Status\StatusService;

	class Service
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
		public function fail(?int $statusCode = null, array $data = array(), string $message = null): StatusService {
			return new StatusService(FALSE, $statusCode, $data, $message);
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
		public function success(?int $statusCode = null, array $data = array(), string $message = null): StatusService {
			return new StatusService(TRUE, $statusCode, $data, $message);
		}
	}