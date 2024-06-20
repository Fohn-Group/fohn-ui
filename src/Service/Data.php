<?php

declare(strict_types=1);

/**
 * Singleton for Data service through Ui.
 *
 * Responsible for creating proper Model Controller to use with Ui Views.
 */

namespace Fohn\Ui\Service;

use Atk4\Data\Model;
use Fohn\Ui\Service\Atk\FormModelController;
use Fohn\Ui\Service\Atk\TableModelController;

class Data
{
    protected static ?Data $instance = null;

    protected string $formModelControllerClass = FormModelController::class;
    protected string $tableModelControllerClass = TableModelController::class;

    /** @var mixed */
    private $db;

    final private function __construct() {}

    public static function get(): self
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param mixed $db
     */
    public static function setDb($db): void
    {
        static::get()->setDbPersistence($db);
    }

    public static function formModelCtrl(Model $model): FormModelControllerInterface
    {
        return static::get()->factoryFormModelCtrl($model);
    }

    public static function tableModelCtrl(Model $model): TableModelControllerInterface
    {
        return static::get()->factoryTableModelCtrl($model);
    }

    /**
     * @return mixed
     */
    public static function db()
    {
        return static::get()->getDb();
    }

    protected function factoryFormModelCtrl(Model $model): FormModelControllerInterface
    {
        return new $this->formModelControllerClass($model);
    }

    protected function factoryTableModelCtrl(Model $model): TableModelControllerInterface
    {
        return new $this->tableModelControllerClass($model);
    }

    /**
     * @param mixed $db
     */
    protected function setDbPersistence($db): void
    {
        $this->db = $db;
    }

    /**
     * @return mixed
     */
    protected function getDb()
    {
        return $this->db;
    }

    public function setFormModelCtrlClassName(string $className): void
    {
        $this->formModelControllerClass = $className;
    }

    public function setTableModelCtrlClassName(string $className): void
    {
        $this->tableModelControllerClass = $className;
    }
}
