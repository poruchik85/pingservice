<?php

namespace Models;

use ErrorException;
use JsonException;
use JsonSerializable;
use LogicException;
use Services\ConfigReader;
use Services\Connector;
use Services\PgConnector;
use Services\QueryBuilder;

abstract class Model implements JsonSerializable
{
    private const TABLE_NAME = "";
    private const FIELDS = [];
    protected const REQUIRED_FIELDS = [];
    protected const IMMUTABLE_FIELDS = [];
    protected const CREATED_AT_FIELD = "created_at";

    /**
     * @var Connector $connector
     */
    protected static $connector;

    /**
     * @var array $fields
     */
    protected $fields = [];

    /**
     * @param $data
     *
     * @throws ErrorException
     */
    public function __construct($data) {
        $this->checkRequired($data);

        $this->fillObject($data);
    }

    /**
     * @param array $data
     *
     * @return Model
     */
    public static function create(array $data): Model {
        $actualClass = static::class;

        /** @var Model $model */
        $model = new $actualClass($data);

        $created_at = self::CREATED_AT_FIELD;

        if (in_array($created_at, static::FIELDS) && (!isset($model->$created_at) || $model->$created_at === null)) {
            $model->$created_at = date('Y-m-d H:i:s');
        }

        if (!isset($model->id) || $model->id === null) {
            $model->save();
        }

        return $model;
    }

    /**
     * @param int $id
     *
     * @return Model|null
     */
    public static function find(int $id): ?self {
        $query = QueryBuilder::select(
            static::TABLE_NAME,
            ["id" => $id]
        );

        $result = self::$connector->select($query);

        if (count($result) === 0) {
            return null;
        }

        return self::create($result[0]);
    }

    /**
     * @return array
     */
    public static function list(): array {
        $query = QueryBuilder::select(
            static::TABLE_NAME,
            [],
            "created_at",
            "desc"
        );

        $result = self::$connector->select($query);

        return array_map(function ($item) {
            return self::create($item);
        }, $result);
    }

    /**
     * @param array $data
     *
     * @throws ErrorException
     */
    protected function checkRequired(array $data) {
        foreach (static::REQUIRED_FIELDS as $field) {
            if (!isset($data[$field])) {
                throw new ErrorException(sprintf("Required field missing: \"%s\"", $field));
            }
        }
    }

    /**
     * @param array $data
     */
    protected function fillObject(array $data) {
        foreach ($data as $field => $value) {
            if (in_array($field, static::FIELDS)) {
                $this->fields[$field] = $value;
            }
        }
    }

    public function save(): void {
        if (isset($this->id)) {
            $query = QueryBuilder::update(
                static::TABLE_NAME,
                ["id" => $this->id],
                $this->fields
            );
        } else {
            $query = QueryBuilder::insert(
                static::TABLE_NAME,
                $this->fields
            );
        }

        $this->id = self::$connector->insert($query);
    }

    /**
     * @param array $data
     */
    public function update(array $data): void {
        foreach ($data as $key => $value) {
            if (in_array($key, static::FIELDS) && !in_array($key, static::IMMUTABLE_FIELDS)) {
                $this->$key = $value;
            }
        }

        $this->save();
    }

    public function delete(): void {
        $query = QueryBuilder::delete(
            static::TABLE_NAME,
            ["id" => $this->id]
        );

        self::$connector->execute($query);
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize() {
        return $this->fields;
    }

    /**
     * @param $field
     *
     * @return array
     */
    public function __get($field) {
        if (!in_array($field, static::FIELDS)) {
            throw new LogicException("Invalid field request");
        }
        if (isset($this->fields[$field])) {
            return $this->fields[$field];
        } else {
            return null;
        }
    }

    /**
     * @param $field
     *
     * @param $value
     *
     * @return void
     */
    public function __set($field, $value) {
        if (!in_array($field, static::FIELDS)) {
            throw new LogicException("Invalid field request");
        }

        $this->fields[$field] = $value;
    }

    /**
     * @param $field
     *
     * @return bool
     */
    public function  __isset($field)
    {
        return isset($this->fields[$field]) && $this->fields[$field] !== null;
    }

    /**
     * @throws JsonException
     */
    static function initStatic() {
        $config = new ConfigReader();
        self::$connector = new PgConnector($config->dbConnectionString);
    }
}
