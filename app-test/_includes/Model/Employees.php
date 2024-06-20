<?php

declare(strict_types = 1);
/**
 * Employees
 */

namespace Fohn\Ui\AppTest\Model;

use Atk4\Data\Model;

class Employees extends Model
{
    public $table = 'employees';

//    public ?string $titleField = 'first_name';

    protected function init(): void
    {
        parent::init();
        $this->addExpression('name', ['expr' => 'concat(last_name, ", ", first_name)']);
        $this->addField('first_name', ['caption' => 'First Name', 'type' => 'string']);
        $this->addField('last_name', ['caption' => 'Last Name', 'type' => 'string']);
        $this->addField('email', ['caption' => 'Email', 'type' => 'string']);
        $this->addField('title', ['caption' => 'Title', 'type' => 'string']);
        $this->addField('city', ['caption' => 'City', 'type' => 'string']);
        $this->addField('state', ['caption' => 'State', 'type' => 'string']);
        $this->addField('country', ['caption' => 'Country', 'type' => 'string']);
        $this->addField('birth_date', ['caption' => 'Birthdate', 'type' => 'date']);
        $this->addField('salary', ['caption' => 'Gender', 'type' => 'atk4_money']);
    }
}
