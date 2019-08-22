<?php
	/**
	 * Created by PhpStorm.
	 * User: fabrizio
	 * Date: 26/07/18
	 * Time: 19.06
	 */

	namespace Kosmosx\Support\Repository\Contracts;

	interface RepositoryInterface {

		public function all($relationships = array(), $columns = array('*'));

		public function paginate(int $perPage = 15, $relationships = array(), $columns = array('*'), $query = "", $value = null);

		public function create(array $data);

		public function update(array $data, $attribute, string $model_attribute = "id");

		public function delete($id);

		public function find($id, $relationships = array(), $columns = array('*'));

		public function findBy($attribute, string $model_attribute = 'id', $relationships = array(), $columns = array('*'));

		public function validate(array $request, array $rules = array(), array $messages = array());
	}