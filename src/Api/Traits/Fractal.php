<?php
	/**
	 * Created by PhpStorm.
	 * User: fabrizio
	 * Date: 20/10/18
	 * Time: 16.54
	 */

	namespace Kosmosx\Support\Api\Traits;

	use League\Fractal\TransformerAbstract;
	use League\Fractal\Pagination\IlluminatePaginatorAdapter;
	use League\Fractal\Resource\Item;
	use League\Fractal\Resource\Collection;
	use League\Fractal\Manager;
	use League\Fractal\Serializer\ArraySerializer;

	trait Fractal
	{
		private $fractal;

		public function __construct()
		{
			$this->fractal = new Manager();
		}

		/**
		 * Create collection with data Transformer
		 *
		 * @param $data (instance of Collection)
		 * @param $transformer (class of Transformer)
		 * @param null $includesData (array or string to add data to collection
		 * @param null $serializer (class of Serialize)
		 * @return mixed
		 */
		public function collection($data, TransformerAbstract $transformer, ?array $includesData = null, ArraySerializer $serializer = null)
		{
			$resource = new Collection($data, $transformer);
			return $this->transform($resource, $serializer, $includesData);
		}

		/**
		 * @param $data (instance of Paginate)
		 * @param $transformer (class of Transformer)
		 * @param null $includesData (array or string to add data to collection
		 * @param null $serializer (class of Serialize)
		 * @return mixed
		 */
		public function paginate($data,TransformerAbstract $transformer, ?array  $includesData = null, ArraySerializer $serializer = null)
		{
			$resource = new Collection($data->getCollection(), $transformer);
			$resource->setPaginator(new IlluminatePaginatorAdapter($data));
			return $this->transform($resource, $serializer, $includesData);
		}

		/**
		 * @param $data
		 * @param $transformer (class of Transformer)
		 * @param null $includesData (array or string to add data to collection
		 * @param null $serializer (class of Serialize)
		 * @return mixed
		 */
		public function item($data,TransformerAbstract $transformer, ?array  $includesData = null, ArraySerializer $serializer = null)
		{
			$resource = new Item($data, $transformer);
			return $this->transform($resource, $serializer, $includesData);
		}

		/**
		 * @param $serializer
		 * @return $this
		 */
		public function serializer(ArraySerializer $serializer){
			$this->fractal->setSerializer($serializer);
			return $this;
		}

		/**
		 * @param $includes
		 * @return $this
		 */
		public function includes(array $includes){
			$this->fractal->parseIncludes($includes);
			return $this;
		}

		/**
		 * @param $excludes
		 * @return $this
		 */
		public function excludes(array $excludes){
			$this->fractal->parseExcludes($excludes);
			return $this;
		}

		/**
		 * @param $limit
		 * @return $this
		 */
		public function recursionLimit($limit){
			$this->fractal->setRecursionLimit($limit);
			return $this;
		}

		/**
		 * @param $data
		 * @param $serializer
		 * @param $includesData
		 * @return mixed
		 */
		private function transform($data, ?ArraySerializer $serializer, ?array $includesData){
			if($serializer)
				$this->serializer($serializer);
			if($includesData)
				$this->includes($includesData);
			return $this->fractal->createData($data)->toArray();
		}
	}