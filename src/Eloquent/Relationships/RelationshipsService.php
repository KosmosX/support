<?php

	namespace Kosmosx\Support\Eloquent\Relationships;

	use ErrorException;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\Relation;
	use phpDocumentor\Reflection\Types\Boolean;
	use ReflectionClass;
	use ReflectionMethod;
	use Illuminate\Support\Collection;

	/**
	 * Class Relationships
	 * @package Kosmosx\Support\Eloquent\Relationships
	 */
	class Relationships
	{
		private $model;
		private $relationships;

		public function __construct(Model $model)
		{
			$this->model = $model;
			$this->relationships = new Collection;
		}

		/**
		 * @param string|null $needle
		 * @return Collection|null
		 * @throws \ReflectionException
		 */
		public function __invoke(?string $needle = null)
		{
			if (null == $this->relationships)
				$this->load();

			if (null != $needle)
				return $this->search($needle);

			return $this->relationships;
		}

		/**
		 * Get all relationships of model
		 *
		 * @return Collection
		 * @throws \ReflectionException
		 */
		public function load(bool $force = false): self
		{
			//metodi pubblici del modello selezionato
			$MODEL_METHODS = (new ReflectionClass($this->model))->getMethods(ReflectionMethod::IS_PUBLIC);

			foreach ($MODEL_METHODS as $method) {
				if ($method->class == get_class($this->model) && empty($method->getParameters()) && $method->getName() !== __FUNCTION__) {
					try {
						$invoked = $method->invoke($this->model);

						//se il metodo invocato è una Relation di Laravel
						if ($invoked instanceof Relation) {
							$ownerKey = null;

							if ((new ReflectionClass($invoked))->hasMethod('getOwnerKey'))
								$ownerKey = $invoked->getOwnerKey();
							else {
								$segments = explode('.', $invoked->getQualifiedParentKeyName());
								$ownerKey = $segments[count($segments) - 1];
							}

							$structure = array(
								'name' => $method->getName(),
								'type' => (new ReflectionClass($invoked))->getShortName(),
								'model' => (new ReflectionClass($invoked->getRelated()))->getName(),
								'foreignKey' => (new ReflectionClass($invoked))->hasMethod('getForeignKey') ? $invoked->getForeignKey() : $invoked->getForeignKeyName(),
								'ownerKey' => $ownerKey
							);

							$rel = new RelationshipStructure($structure);

							$this->relationships[$rel->name] = $rel;
						}
					} catch (ErrorException $e) {

					}
				}
			}

			return $this;
		}

		/**
		 * @param bool $reload
		 * @return Collection
		 *
		 * @throws \ReflectionException
		 */
		public function all(bool $reload = false): Collection
		{
			if (true === $reload)
				$this->load();

			return $this->relationships;
		}

		/**
		 * @param array|string $needle
		 * @param bool $reload
		 *
		 * @return Collection
		 * @throws \ReflectionException
		 */
		public function get($needles, bool $reload = false): Collection
		{
			if (true === $reload)
				$this->load();

			return $this->search($needles);
		}

		/**
		 * Search by name of relationship or by namespace of Model
		 *
		 * @param array|string $needles
		 *
		 * @return Collection
		 * @throws \Exception
		 */
		public function search($needles):Collection
		{
			if (is_string($needles))
				$needles = (array)$needles;

			if (false === is_array($needles))
				throw new \Exception();

			$relationships = new Collection;

			foreach ($this->relationships as $name => $relationship) {
				foreach ($needles as $needle)
					if ($needle === $name || $needle === $relationship->model)
						$relationships[$name] = $relationship;
			}

			return $relationships;
		}
	}