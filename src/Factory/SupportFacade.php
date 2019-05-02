<?php
	/**
	 * Created by PhpStorm.
	 * User: fabrizio
	 * Date: 09/08/18
	 * Time: 17.42
	 */
	namespace Kosmosx\Support\Factory;

	use Illuminate\Support\Facades\Facade;

	class SupportFacade extends Facade
	{
		protected static function getFacadeAccessor()
		{
			return 'factory.support';
		}
	}