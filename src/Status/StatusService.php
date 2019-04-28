<?php
	/**
	 * Created by PhpStorm.
	 * User: fabrizio
	 * Date: 27/12/18
	 * Time: 11.11
	 */

	namespace Kosmosx\Support\Status;


	class StatusService
	{
		/**
		 * @var bool
		 */
		protected $success;

		/**
		 * @var array
		 */
		protected $data;

		/**
		 * @var string
		 */
		protected $message;

		/**
		 * @var string
		 */
		protected $statusCode;

		/**
		 *  Default constructor.
		 *
		 * @param bool   $success
		 * @param int $statusCode
		 * @param array  $data
		 * @param string $message
		 */
		public function __construct(bool $success = null, ?int $statusCode = null, ?array $data = array(), ?string $message = null)
		{
			$this->success = $success;
			$this->data = $data;
			$this->message = $message;
			$this->statusCode = $statusCode;
		}

		/**
		 *
		 *  Instances factory.
		 *
		 * @param bool   $success
		 * @param int $statusCode
		 * @param array  $data
		 * @param string $message
		 *
		 * @return StatusService
		 *    The instance.
		 */
		public static function set(bool $success, ?int $statusCode = null, ?array $data = array(), ?string $message = null): StatusService
		{
			return new StatusService($success, $statusCode, $data, $message);
		}

		/**
		 * @param string|array $key
		 *    Optional key in the data associative array.
		 *    Key maybe use DOT notation
		 *
		 * @return array
		 *    The data array or the requested item if $key is set.
		 */
		public function data($keys = null):array
		{

			if (is_string($keys))
				$keys = (array) $keys;

			if (is_array($keys)) {
				$data = array();
				foreach ($keys as $key)
					$data[last(explode('.', $key))] = array_get($this->data, $key, null);

				return $data;
			}

			return $this->data;
		}

		/**
		 * @param int $withStatusCode
		 *    The status code to be searched.
		 *
		 * @return bool
		 *    TRUE if service method failed.
		 *    When $with is provided, returns TRUE when
		 *    method failed (AND) with the specified
		 *    status code.
		 */
		public function isFail(int $withStatusCode = null): bool
		{
			if ($withStatusCode) {
				return !$this->success && $withStatusCode === $this->statusCode;
			}

			return !$this->success;
		}

		/**
		 * @return string
		 *        The message.
		 */
		public function message(): ?string
		{
			return $this->message;
		}

		/**
		 * @return int
		 *        The service status.
		 */
		public function status(): ?int
		{
			return $this->statusCode;
		}

		/**
		 * @param string $with
		 *    The status code to be searched.
		 *
		 * @return bool
		 *    TRUE if service method ran successfully.
		 *    When $with is provided, returns TRUE when
		 *    method ran successfully AND with the specified
		 *    status code.
		 */
		public function isSuccess(int $withStatusCode = null): bool
		{
			if ($withStatusCode) {
				return $this->success && $withStatusCode === $this->statusCode;
			}

			return $this->success;
		}

		public function toArray()
		{
			return array(
				'success' => $this->success,
				'data' => $this->data,
				'message' => $this->message,
				'statusCode' => $this->statusCode,
			);
		}

		public function __toString()
		{
			$toString = json_encode($this->toArray(),JSON_FORCE_OBJECT);
			return $toString;
		}

	}