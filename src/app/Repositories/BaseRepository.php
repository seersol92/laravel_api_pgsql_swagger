<?php

namespace App\Repositories;

use App\Models\BaseModel;

use Exception;
use function PHPUnit\Framework\isNull;
use Illuminate\Support\Str;

class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var BaseModel
     */
    protected BaseModel $model;

    /**
     * BaseRepository constructor.
     * @param BaseModel $model
     */
    public function __construct(?BaseModel $model = null)
    {
        if (isNull($model)) {
            /* if we don't do binding this will try to assign model instance depending upon the repo name
            for it to work the repositories in the Repository folder must have repository in the name
            then it will get its name and check that whether the name behind repository is a class then assign that
            */
            $className = strtolower(get_class($this));
            $isRepoNameIncludedInFile = Str::contains($className, 'repository');
            throw_unless($isRepoNameIncludedInFile, new Exception('Repository name not inclued in the file name. need to provide the base Model in the constructor'));
            if ($isRepoNameIncludedInFile) {
                $modelName = Str::afterLast(Str::beforeLast($className, 'repository'), '\\');
                $modelName = Str::headline($modelName);
                if (class_exists("App\\Models\\{$modelName}")) {
                    $model = app("App\\Models\\{$modelName}");
                } else {
                    throw new Exception("Model '{$modelName}' not found");
                }
            }

        }

        $this->model = $model;

    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function all(array $columns = array('*')): mixed
    {
        try {
            return $this->model->get($columns);
        } catch (\Exception $exception) {
            //Logger::log(LogLevel::CRITICAL, "repositoryError - all", $exception->getMessage(), $exception->getTraceAsString());
            return false;
        }
    }

    /**
     * @param array $dataArray
     * @return mixed
     */
    public function addMany(array $dataArray): mixed
    {
        try {
            $result = [];
            foreach ($dataArray as $data) {
                $createdItem = $this->model->create($data);
                $result[$createdItem->id] = $data;
            }
            return $result;
        } catch (\Exception $exception) {
            //Logger::log(LogLevel::CRITICAL, "repositoryError - addMany", $exception->getMessage(), $exception->getTraceAsString());
            return false;
        }
    }

    /**
     * @param array $dataArray
     * @return mixed
     */
    public function addBulk(array $dataArray): mixed
    {
        try {
            return $this->model->createMany($dataArray);
        } catch (\Exception $exception) {
            //Logger::log(LogLevel::CRITICAL, "repositoryError - addBulk", $exception->getMessage(), $exception->getTraceAsString());
            return false;
        }
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function add(array $data): mixed
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $exception) {
            print_r($exception->getMessage());
            exit;
            return false;
        }
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data): mixed
    {
        $row = $this->model->find($id);
        if (!$row) {
            return false;
        }

        try {
            return $row->fill($data)->save();
        } catch (\Exception $exception) {
            //Logger::log(LogLevel::CRITICAL, "repositoryError - update", $exception->getMessage(), $exception->getTraceAsString());
            return false;
        }
    }

    /**
     * @param array $attributes
     * @param array $data
     * @return bool
     */
    public function updateBy(array $attributes, array $data): mixed
    {
        try {
            return $this->model->where($attributes)->update($data);
        } catch (\Exception $exception) {
            //Logger::log(LogLevel::CRITICAL, "repositoryError - updateBy", $exception->getMessage(), $exception->getTraceAsString());
            return false;
        }
    }

    /**
     * @param BaseModel $model
     * @return mixed
     */
    public function delete(BaseModel $model): mixed
    {
        try {
            return $model->delete();
        } catch (\Exception $exception) {
            //Logger::log(LogLevel::CRITICAL, "repositoryError - delete", $exception->getMessage(), $exception->getTraceAsString());
            return false;
        }
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function deleteByAttributes(array $attributes): mixed
    {
        try {
            return $this->model->where($attributes)->delete();
        } catch (\Exception $exception) {
            //Logger::log(LogLevel::CRITICAL, "repositoryError - deleteByAttributes", $exception->getMessage(), $exception->getTraceAsString());
            return false;
        }

    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, array $columns = array('*')): mixed
    {
        try {
            return $this->model->find($id);
        } catch (\Exception $exception) {
            //Logger::log(LogLevel::CRITICAL, "repositoryError - find", $exception->getMessage(), $exception->getTraceAsString());
            return false;
        }
    }

    /**
     * @param array $attributes
     * @param array $with
     * @param string[] $columns
     * @return mixed
     */

    public function findByAttributes(array $attributes, array $with = [], array $columns = array("*")): mixed
    {
        $query = $this->queryBuilder($attributes);

        if (!empty($with)) {
            foreach ($with as $relation) {
                $query = $query->with($relation);
            }
        }

        try {
            return $query->first($columns);
        } catch (\Exception $exception) {
            //Logger::log(LogLevel::CRITICAL, "repositoryError - findByAttributes", $exception->getMessage(), $exception->getTraceAsString());
            return false;
        }
    }

    /**
     * @param array $attributes
     * @param mixed $orderBy
     * @param string $sortOrder
     * @param array $with
     * @param bool $withTrashed
     * @param array $columns
     * @return mixed
     */
    public function getManyByAttributes(array $attributes, mixed $orderBy = null, string $sortOrder = 'asc', array $with = [], bool $withTrashed = false, array $columns = array("*")): mixed
    {
        $query = $this->queryBuilder($attributes, $orderBy, $sortOrder, $withTrashed);

        if (!empty($with)) {
            foreach ($with as $relation) {
                $query = $query->with($relation);
            }
        }

        try {
            return $query->get($columns);
        } catch (\Exception $exception) {
            //Logger::log(LogLevel::CRITICAL, "repositoryError - getManyByAttributes", $exception->getMessage(), $exception->getTraceAsString());
            return false;
        }
    }

    /**
     * @param array $attributes
     * @param bool $withTrashed
     * @return mixed
     */
    public function getManyByAttributeCounts(array $attributes, bool $withTrashed = false): mixed
    {
        try {
            $query = $this->queryBuilder($attributes, null, 'asc', $withTrashed);
            return $query->count();

        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param array $attributes
     * @param mixed|null $orderBy
     * @param string $sortOrder
     * @param bool $withTrashed
     * @return mixed
     */
    protected function queryBuilder(array $attributes, mixed $orderBy = null, string $sortOrder = 'asc', bool $withTrashed = false): mixed
    {
        $query = $this->model->query();

        foreach ($attributes as $field => $value) {

            switch ($field) {
                case "whereIn":
                    $query = $query->whereIn(key($value), $value[key($value)]);
                    break;
                case "whereJsonContains":
                    foreach ($value as $col => $json) {
                        $query = $query->whereJsonContains($col, $json);
                    }
                    break;
                case "limit":
                    $query = $query->limit($value);
                    break;
                case "offset":
                    $query = $query->offset($value);
                    break;
                case "like":
                    foreach ($value as $col => $like) {
                        $query = $query->where($col, 'ILIKE', $like);
                    }
                    break;
                case "not":
                    foreach ($value as $col => $not) {
                        $query = $query->where($col, '!=', $not);
                    }
                    break;
                case "between":
                    $query = $query->whereBetween(key($value), $value[key($value)]);
                    break;
                case "whereHas":
                    foreach ($value as $first => $relations) {
                        foreach ($relations as $col => $like) {
                            $query = $query->whereHas($first, function ($q) use ($col, $like) {
                                $q->where($col, $like);
                            });
                        }
                    }
                    break;
                case "whereHasLike":
                    foreach ($value as $first => $relations) {
                        foreach ($relations as $col => $like) {
                            $query = $query->whereHas($first, function ($q) use ($col, $like) {
                                $q->where($col, 'ILIKE', '%' . $like . '%');
                            });
                        }
                    }
                    break;
                default:
                    $query = $query->where($field, $value);
                    break;
            }
        }

        if (null !== $orderBy) {
            if (!is_array($orderBy)) {
                $orderBy = [$orderBy];
            }

            foreach ($orderBy as $order) {
                $query->orderBy($order, $sortOrder);
            }
        }

        return $query;
    }

}
