<?php


namespace App\Form;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class SearchForm extends Form
{
    protected function _buildSchema(Schema $schema): Schema
    {
        return $schema->addField('name', 'string')
            ->addField('from[latitude]', ['type' => 'string'])
            ->addField('from[longitude]', ['type' => 'string'])
            ->addField('from[fulladrress]', ['type' => 'string'])
            ->addField('to[latitude]', ['type' => 'string'])
            ->addField('to[longitude]', ['type' => 'string'])
            ->addField('to[fulladrress]', ['type' => 'string'])
            ->addField('seats[number]', ['type' => 'integer']);
    }

    public function validationDefault(Validator $validator): Validator
    {
        /*$validator->minLength('name', 10)
            ->email('email');*/

        return $validator;
    }

    protected function _execute(array $data): bool
    {
        // Send an email.
        return true;
    }
}
