<?php

declare(strict_types=1);
/**
 * Demo model.
 */

namespace Fohn\Ui\AppTest\Model;

use Atk4\Data\Model;

class Country extends Model
{
    public $table = 'country';

    protected function init(): void
    {
        parent::init();
        $this->addField('name', ['actual' => 'nicename', 'required' => true, 'type' => 'string']);
        $this->addField('sys_name', ['actual' => 'name', 'system' => true]);

        $this->addField('iso', ['caption' => 'ISO', 'required' => true, 'type' => 'string', 'ui' => ['table' => ['sortable' => false]]]);
        $this->addField('iso3', ['caption' => 'ISO3', 'required' => true, 'type' => 'string']);
        $this->addField('numcode', ['caption' => 'ISO Numeric Code', 'type' => 'integer', 'required' => true]);
        $this->addField('phonecode', ['caption' => 'Phone Prefix', 'type' => 'integer', 'required' => true]);

        $this->onHook(Model::HOOK_BEFORE_SAVE, function (Model $model) {
            if (!$model->get('sys_name')) {
                $model->set('sys_name', mb_strtoupper($model->get('name')));
            }
        });
    }

    public function validate(string $intent = null): array
    {
        $errors = parent::validate($intent);

        if (mb_strlen($this->get('iso')) !== 2) {
            $errors['iso'] = 'Must be exactly 2 characters';
        }

        if (mb_strlen($this->get('iso3')) !== 3) {
            $errors['iso3'] = 'Must be exactly 3 characters';
        }

        // look if name is unique
        $c = $this->getModel()->tryLoadBy('name', $this->get('name'));
        if ($c && $c->getId() !== $this->getId()) {
            $errors['name'] = 'Country name must be unique';
        }

        return $errors;
    }
}
