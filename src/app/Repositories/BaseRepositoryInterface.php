<?php

namespace App\Repositories;

use App\Models\BaseModel;

interface BaseRepositoryInterface
{
    /**
     * @param array $columns
     * @return mixed
     */
    public function all(array $columns = array('*')): mixed;

    /**
     * @param array $data
     * @return mixed
     */
    public function add(array $data): mixed;

    /**
     * @param array $dataArray
     * @return mixed
     */
    public function addMany(array $dataArray): mixed;

    /**
     * @param array $dataArray
     * @return mixed
     */
    public function addBulk(array $dataArray): mixed;

    /**
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data): mixed;

    /**
     * @param array $attributes
     * @param array $data
     * @return mixed
     */
    public function updateBy(array $attributes, array $data): mixed;

    /**
     * @param BaseModel $model
     * @return mixed
     */
    public function delete(BaseModel $model): mixed;

    /**
     * @param array $attributes
     * @return mixed
     */
    public function deleteByAttributes(array $attributes): mixed;

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, array $columns = array('*')): mixed;

    /**
     * @param array $attributes
     * @param array $with
     * @param array $columns
     * @return mixed
     */
    public function findByAttributes(array $attributes, array $with = [], array $columns = array("*")): mixed;

    /**
     * @param array $attributes
     * @param null $orderBy
     * @param string $sortOrder
     * @param array $with
     * @param bool $withTrashed
     * @param string[] $columns
     * @return mixed
     */
    public function getManyByAttributes(array $attributes, mixed $orderBy = null, string $sortOrder = 'asc', array $with = [], bool $withTrashed = false, array $columns = array("*")): mixed;

    /**
     * @param array $attributes
     * @param bool $withTrashed
     * @return mixed
     */
    public function getManyByAttributeCounts(array $attributes, bool $withTrashed = false): mixed;
}
