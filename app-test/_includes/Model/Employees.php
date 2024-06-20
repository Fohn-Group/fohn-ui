<?php

declare(strict_types=1);
/**
 * Employees.
 */

namespace Fohn\Ui\AppTest\Model;

use Atk4\Data\Model;
use Fohn\Ui\Component\Table\Column\Currency;
use Fohn\Ui\Component\Table\Column\Date;
use Fohn\Ui\Component\Table\Column\Generic;
use Fohn\Ui\Component\Table\Filter\Number;
use Fohn\Ui\Component\Table\Filter\Text;
use Fohn\Ui\Service\Ui;

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

    public function getTableColumns(): array
    {
        return [
            'name' => Generic::factory(['isSortable' => true]),
            'title' => Generic::factory(['isSortable' => true]),
            'city' => Generic::factory(['isSortable' => true]),
            'state' => Generic::factory(['isSortable' => true]),
            'birth_date' => Date::factory(['isSortable' => true, 'caption' => 'Birthdate', 'format' => Ui::getDisplayFormat('date')]),
            'salary' => Currency::factory(['isSortable' => true]),
        ];
    }

    public function getTableFilters(): array
    {
        return [
            new Text('name'),
            new Text('first_name', 'First Name'),
            new Text('last_name', 'Last Name'),
            new \Fohn\Ui\Component\Table\Filter\Date('birth_date', Ui::getDisplayFormat('date'), 'Birthdate'),
            new Number('salary', 'Salary'),
        ];
    }
}
