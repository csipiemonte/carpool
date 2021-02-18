<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Providers Model
 *
 * @property \App\Model\Table\SearchResultsTable&\Cake\ORM\Association\HasMany $SearchResults
 *
 * @method \App\Model\Entity\Provider newEmptyEntity()
 * @method \App\Model\Entity\Provider newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Provider[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Provider get($primaryKey, $options = [])
 * @method \App\Model\Entity\Provider findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Provider patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Provider[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Provider|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Provider saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Provider[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Provider[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Provider[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Provider[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ProvidersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('providers');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('SearchResults', [
            'foreignKey' => 'provider_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 64)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('description')
            ->maxLength('description', 128)
            ->allowEmptyString('description');

        $validator
            ->scalar('url')
            ->maxLength('url', 256)
            ->requirePresence('url', 'create')
            ->notEmptyString('url');

        $validator
            ->scalar('apikey')
            ->maxLength('apikey', 64)
            ->requirePresence('apikey', 'create')
            ->notEmptyString('apikey');

        $validator
            ->scalar('privatekey')
            ->maxLength('privatekey', 64)
            ->requirePresence('privatekey', 'create')
            ->notEmptyString('privatekey');

        $validator
            ->scalar('api')
            ->maxLength('api', 64)
            ->requirePresence('api', 'create')
            ->notEmptyString('api');

        $validator
            ->scalar('data')
            ->requirePresence('data', 'create')
            ->notEmptyString('data');

        $validator
            ->scalar('url_icona')
            ->maxLength('url_icona', 200)
            ->allowEmptyString('url_icona');

        $validator
            ->scalar('homepage')
            ->maxLength('homepage', 60)
            ->allowEmptyString('homepage');

        return $validator;
    }
}
