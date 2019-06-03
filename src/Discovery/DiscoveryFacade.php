<?php
	namespace Kosmosx\Support\Discovery;

	use Illuminate\Support\Facades\Facade;

	class DiscoveryFacade extends Facade
	{
		protected static function getFacadeAccessor()
		{
			return 'service.discovery';
		}
	}