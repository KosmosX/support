<?php

	namespace Kosmosx\Support\Repository\Eloquent;

	use Kosmosx\Support\Repository\Contracts\RepositoryInterface;
	use Kosmosx\Support\Repository\Exceptions\RepositoryException;
	use Kosmosx\Support\Eloquent\Relationships\Relationships;
	use Illuminate\Container\Container as App;
	use Illuminate\Support\Collection;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Validator;

	/**
	 * Class Repository
	 * @package Bosnadev\Repositories\Eloquent
	 */
	abstract class RepositoryAbstract implements RepositoryInterface
	{
		public $RULES = array();
		public $MESSAGES = array();
		/**
		 * @var Model
		 */
		protected $model;
		protected $relationships = null;

		/**
		 * @param App $app
		 */
		public function __construct()
		{
			$this->init();
		}

		/**
		 * @return Model
		 */
		private function init()
		{
			$model = app($this->model());

			if (!$model instanceof Model)
				throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");

			$this->model = $model;

			$this->loadRelationships();

			return $this->model;
		}

		/**
		 * Specify Model class name
		 *
		 * @return mixed
		 */
		abstract function model();

		/**
		 * @param array $columns
		 *
		 * @return mixed
		 */
		public function all($relationships = array(), $columns = array('*'))
		{
			$data = $this->model->all($columns);

			if ($relationships)
				return $this->withRelationships($data, $relationships);

			return $data;
		}

		/**
		 * @param int $perPage
		 * @param array $columns
		 *
		 * @return mixed
		 */
		public function paginate(int $perPage = 15, $relationships = array(), $columns = array('*'), $query = "", $value = null)
		{
			$data = $this->model->paginate($perPage, $columns, $query, $value);

			if ($relationships)
				return $this->withRelationships($data, $relationships);

			return $data;
		}

		/**
		 * @param array $data
		 *
		 * @return mixed
		 */
		public function create(array $data)
		{
			return $this->model->create($data);
		}

		/**
		 * @param array $data
		 * @param        $id
		 * @param string $attribute
		 *
		 * @return mixed
		 */
		public function update(array $data, $attribute, $columns = array('*'))
		{
			$model = $this->findBy($attribute, $columns);
			return $model->update($data);
		}

		/**
		 * @param       $attribute
		 * @param       $value
		 * @param array $columns
		 *
		 * @return mixed
		 */
		public function findBy($attribute, string $model_attribute = 'id', $relationships = array(), $columns = array('*'))
		{
			$data = $this->model->where($model_attribute, '=', $attribute)->get($columns);

			if ($relationships)
				return $this->withRelationships($data, $relationships);

			return $data;
		}

		/**
		 * @param $id
		 *
		 * @return mixed
		 */
		public function delete($id)
		{
			return $this->model->destroy($id);
		}

		/**
		 * @param       $id
		 * @param array $columns
		 *
		 * @return mixed
		 */
		public function find($id, $relationships = array(), $columns = array('*'))
		{
			$data = $this->model->find($id, $columns);

			if ($relationships)
				return $this->withRelationships($data, $relationships);

			return $data;
		}

		/**
		 * @param $model
		 * @param $relationships
		 * @return mixed
		 * @throws \Exception
		 */
		public function withRelationships($model, $relationships)
		{
			if (!($model instanceof Model) && null == $relationships)
				throw new \Exception();

			$rels = $this->relationships->get($relationships);

			foreach ($rels as $rel => $item)
				$model->load($rel);

			return $model;
		}

		/**
		 * @param array $request
		 * @param array $rules
		 * @param array $messages
		 *
		 * @return array
		 */
		public function validate(array $request, array $rules = array(), array $messages = array())
		{
			$rules = $rules ?: $this->_getAttr('RULES');
			$messages = $messages ?: $this->_getAttr('MESSAGES');

			$validator = Validator::make($request, $rules, $messages);

			if ($validator->fails())
				return $validator->errors()->toArray();

			return array();
		}

		/**
		 * @param Model $model
		 * @param $relationships
		 * @return \Illuminate\Database\Eloquent\Builder|Model
		 */
		protected function loadRelationships()
		{
			$this->relationships = (new Relationships($this->model))->load();

			return $this->relationships->all();
		}

		/**
		 * @param $needles
		 * @return mixed
		 */
		public function getRelationships($needles): Collection
		{
			return $this->relationships->get($needles);
		}

		/**
		 * @return mixed
		 */
		public function allRelationships(): Collection
		{
			return $this->relationships->all();
		}

		/**
		 * @param string $name
		 *
		 * @return mixed|null
		 * @throws \ReflectionException
		 */
		protected function _getAttr(string $name)
		{
			$class = get_called_class();

			$reflection = new \ReflectionClass($class);

			if ($reflection->hasProperty($name))
				return $reflection->getProperty($name)->getValue(new $class);

			return null;
		}
	}