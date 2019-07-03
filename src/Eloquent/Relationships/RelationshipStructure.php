<?php

	namespace Kosmosx\Support\Eloquent\Relationships;

	use Illuminate\Support\Arr;

	/**
	 * Class RelationshipStructure
	 * @package Kosmosx\Support\Eloquent\Relationships
	 */
	class RelationshipStructure
	{
		public $name = null;
		public $type = null;
		public $model = null;
		public $foreignKey = null;
		public $ownerKey = null;

		public function __construct(array $relationship = array())
		{
			$property = Arr::only($relationship, array(
				'name',
				'type',
				'model',
				'foreignKey',
				'ownerKey'
			));

			foreach ($property as $key => $value)
				if(property_exists($this, $key))
					$this->{$key} = $value;
		}

		public function __toString()
		{
			return json_encode($this->toArray(), JSON_FORCE_OBJECT);
		}

		public function toArray()
		{
			return array(
				'name' => $this->name,
				'type' => $this->type,
				'model' => $this->model,
				'foreignKey' => $this->foreignKey,
				'ownerKey' => $this->ownerKey
			);
		}
	}
