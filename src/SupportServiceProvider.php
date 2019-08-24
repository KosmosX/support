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
			register_alias(\Kosmosx\Support\Discovery\DiscoveryFacade::class, 'Discovery');
			register_alias(\Kosmosx\Support\Api\ApiFacade::class, 'Api');

			$this->app->bind('service.api', 'Kosmosx\Support\Api\ApiService');
			$this->app->singleton('service.discovery', 'Kosmosx\Support\Discovery\DiscoveryService');
		}
	}
