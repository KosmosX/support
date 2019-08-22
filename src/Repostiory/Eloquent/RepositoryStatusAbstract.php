<?php

	namespace Kosmosx\Support\Repository\Eloquent;

	use Illuminate\Database\Eloquent\Model;
	use Kosmosx\Support\Repository\Contracts\RepositoryInterface;
	use Kosmosx\Helpers\Status\StatusFactory;
	use Kosmosx\Helpers\Status\StatusService;

	/**
	 * Class Repository
	 * @package Bosnadev\Repositories\Eloquent
	 */
	abstract class RepositoryStatusAbstract extends RepositoryAbstract
	{
		protected $status;

		public function __construct()
		{
			parent::__construct();
			$this->status = new StatusFactory();
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
		public function all($relationships = array(), $columns = array('*')): StatusService
		{
			$data = parent::all($relationships, $columns);

			return $this->status->success(200, $data);
		}

		/**
		 * @param int $perPage
		 * @param array $columns
		 *
		 * @return mixed
		 */
		public function paginate(int $perPage = 15, $relationships = array(), $columns = array('*'), $query = "", $value = null): StatusService
		{
			$data = parent::paginate($perPage, $relationships, $columns, $query, $value);

			return $this->status->success(200, $data);
		}

		/**
		 * @param array $data
		 *
		 * @return mixed
		 */
		public function create(array $data): StatusService
		{
			$data = parent::create($data);

			return $this->status->success(200, $data);
		}

		/**
		 * @param array $data
		 * @param        $id
		 * @param string $attribute
		 *
		 * @return mixed
		 */
		public function update(array $data, $attribute, $columns = array('*')): StatusService
		{
			$status = $this->find($attribute, $columns);

			if ($status->isFail())
				return $status;

			$model = $status->data();

			return $this->updateModel($model,$data);
		}

		public function updateModel(Model $model, $data): StatusService {
			$model->update($data);

			if($model->wasChanged())
				return $this->status->success(200, $model);

			return $this->status->fail(304, $model);
		}

		/**
		 * @param       $attribute
		 * @param       $value
		 * @param array $columns
		 *
		 * @return mixed
		 */
		public function findBy($attribute, string $model_attribute = 'id', $relationships = array(), $columns = array('*')): StatusService
		{
			$data = parent::findBy($attribute, $model_attribute, $relationships, $columns);

			if (null === $data)
				return $this->status->fail(404);
			else
				return $this->status->success(200, $data);
		}

		/**
		 * @param $id
		 *
		 * @return mixed
		 */
		public function delete($id): StatusService
		{
			$data = parent::destroy($id);

			if (false == $data)
				return $this->status->fail(500, array(), 'Internal error');
			else
				return $this->status->success(200);
		}

		/**
		 * @param       $id
		 * @param array $columns
		 *
		 * @return mixed
		 */
		public function find($id, $relationships = array(), $columns = array('*')): StatusService
		{
			$data = parent::find($id, $relationships, $columns);

			if (null === $data)
				return $this->status->fail(404, 'Not found');
			else
				return $this->status->success(200, $data);
		}

		public function validate(array $request, array $rules = array(), array $messages = array())
		{
			$validate = parent::validate($request, $rules, $messages);

			if (empty($validate))
				return $this->status->success();

			return $this->status->fail(400)->setValidate($validate);
		}
	}