<?php

declare(strict_types=1);
/**
 * Simple Model for testing different Field in Form.
 */

namespace Fohn\Ui\AppTest\Model;

use Atk4\Data\Model;
use Fohn\Ui\Component\Form;

class FieldTest extends Model
{
    public $table = 'test_field';
    public ?string $titleField = 'last_name';

    protected function init(): void
    {
        parent::init();

        $this->addField('hidden', ['ui' => ['form' => [Form\Control\Hidden::class, 'inputType' => 'hidden']]]);

        $this->addField('first_name', ['required' => true, 'default' => 'myname']);
        $this->addField('last_name', ['required' => false]);
        $this->hasOne('country_id', [
            'model' => new Country($this->getPersistence()), 'caption' => 'Country',
            'ui' => ['form' => [Form\Control\Select::class]],
        ]);
        $this->addField('radio', ['enum' => ['one', 'two', 'three'], 'default' => 'two', 'ui' => ['form' => [Form\Control\Radio::class]]]);
        $this->addField('email', ['ui' => ['form' => ['inputType' => 'email']]]);
        $this->addField('password', ['ui' => ['form' => [Form\Control\Password::class, 'inputType' => 'password']]]);
        $this->addField('description', ['type' => 'text']);
        $this->addField('date', ['type' => 'date']);
        $this->addField('datetime', ['type' => 'datetime']);
        $this->addField('check_a', ['type' => 'boolean']);
        $this->addField('check_b', ['type' => 'boolean']);
        $this->addField('float', ['type' => 'float', 'ui' => ['form' => ['precision' => 4]]]);
        $this->addField('money', ['type' => 'atk4_money']);
        $this->addField('integer', ['type' => 'integer']);

        $this->addField('caption', [
            'caption' => 'Custom caption:',
            'ui' => ['form' => [Form\Control\Line::class, 'placeholder' => 'Label']],
        ]);
    }

    public function validate(string $intent = null): array
    {
        $errors = parent::validate($intent);
        if (mb_strlen($this->get('first_name') ?? '') < 2) {
            $errors['first_name'] = 'Must be at least 2 characters';
        }

        if (mb_strlen($this->get('last_name') ?? '') < 3) {
            $errors['last_name'] = 'Must be at least 3 characters';
        }

        return $errors;
    }
}
