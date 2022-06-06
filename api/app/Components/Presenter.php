<?php


namespace App\Components;


use App\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JsonSerializable;

abstract class Presenter implements JsonSerializable
{
    private $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    abstract protected function transform(Model $model);

    protected function extend()
    {
        return [];
    }

    public function jsonSerialize()
    {
        if ($this->model === null) {
            return null;
        }

        if ($this->model instanceof Collection) {
            return $this->model->map(function ($model) {
                return $this->serializeModel($model);
            })->values();
        }

        return $this->serializeModel($this->model);
    }

    protected function serializeModel($model)
    {
        $attributes = collect();
        if (count($this->extend()) > 0) {
            foreach ($this->extend() as $extend_presenter) {
                $attributes = $attributes->merge(app($extend_presenter, [$model])->toArray());
            }
        }
        $attributes = $attributes->merge($this->transform($model));
        return $attributes;
    }

    public function toArray()
    {
        return $this->jsonSerialize()->toArray();
    }
}