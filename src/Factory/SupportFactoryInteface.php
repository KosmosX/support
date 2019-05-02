<?php

	namespace Kosmosx\Support\Factory;

	use Kosmosx\Support\Status\StatusService;
	use Kosmosx\Support\Api\ApiService;

	/**
	 * Interface SupportFactoryInteface
	 * @package Kosmosx\Support
	 */
	interface SupportFactoryInteface
	{
		public function fail(?int $statusCode = null, array $data = array(), string $message = null): StatusService;

		public function success(?int $statusCode = null, array $data = array(), string $message = null): StatusService;

		function api(): ApiService;
	}