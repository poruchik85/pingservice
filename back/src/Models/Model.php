<?php

namespace Models;

use ErrorException;
use Exception;
use JsonException;
use JsonSerializable;
use LogicException;
use Services\ConfigReader;
use Services\PgConnector;
use Services\QueryBuilder;

abstract class Model implements JsonSerializable
{
    private const TABLE_NAME = "";
    private const FIELDS = [];
    protected const REQUIRED_FIELDS = [];
    protected const IMMUTABLE_FIELDS = [];

    /**
     * @var PgConnector $connector
     */
    protected static $connector;

    /**
     * @var array $fields
     */
    protected $fields = [];

    /**
     * Model constructor.
     *
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
    public static function create(array $data): Model
    {
        $actualClass = static::class;

        return new $actualClass($data);
    }

    /**
     * @param int $id
     *
     * @return Model|null
     *
     * @throws Exception
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
     *
     * @throws Exception
     */
    public static function list(): array
    {
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
        var_dump($query);
        self::$connector->insert($query);
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize() {
        return get_object_vars($this);
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
        if (!in_array($field, static::FIELDS) || in_array($field, static::IMMUTABLE_FIELDS)) {
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
