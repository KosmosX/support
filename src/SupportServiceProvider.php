<?php

	namespace Kosmosx\Support;

	use Illuminate\Support\ServiceProvider;

	class SupportServiceProvider extends ServiceProvider
	{
		/**
		 * Register any application services.
		 *
		 * @return void
		 */
		public function register()
		{
			class_alias(\Kosmosx\Support\Factory\SupportFacade::class, 'SupportFactory');
			class_alias(\Kosmosx\Support\Api\ApiFacade::class, 'ApiService');
			class_alias(\Kosmosx\Support\Status\StatusFacade::class, 'StatusService');

			$this->app->singleton('factory.support', 'Kosmosx\Support\Factory\SupportFactory');
			$this->app->singleton('service.api', 'Kosmosx\Support\Api\ApiService');
			$this->app->bind('service.status', 'Kosmosx\Support\Status\StatusService');
		}
	}
