<?php

	namespace Kosmosx\Support;

	use Kosmosx\Core\Providers\BaseServiceProvider;

	class SupportServiceProvider extends BaseServiceProvider
	{
		/**
		 * Register any application services.
		 *
		 * @return void
		 */
		public function register()
		{
			$this->registerAlias(array(
				'ApiService' => \Kosmosx\Support\Api\ApiFacade::class,
				'StatusService' => \Kosmosx\Support\Status\StatusFacade::class,
			));

			$this->app->singleton('service.api', 'Kosmosx\Support\Api\ApiService');
			$this->app->singleton('service.status', 'Kosmosx\Support\Status\StatusService');
		}
	}
