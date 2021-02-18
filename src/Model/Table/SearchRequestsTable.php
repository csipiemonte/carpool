<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SearchRequests Model
 *
 * @method \App\Model\Entity\SearchRequest newEmptyEntity()
 * @method \App\Model\Entity\SearchRequest newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SearchRequest[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SearchRequest get($primaryKey, $options = [])
 * @method \App\Model\Entity\SearchRequest findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SearchRequest patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SearchRequest[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SearchRequest|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SearchRequest saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SearchRequest[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SearchRequest[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SearchRequest[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SearchRequest[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SearchRequestsTable extends Table
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

        $this->setTable('search_requests');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->numeric('from_lat')
            ->requirePresence('from_lat', 'create')
            ->notEmptyString('from_lat');

        $validator
            ->numeric('from_lon')
            ->requirePresence('from_lon', 'create')
            ->notEmptyString('from_lon');

        $validator
            ->scalar('from_fulladdress')
            ->maxLength('from_fulladdress', 1024)
            ->allowEmptyString('from_fulladdress');

        $validator
            ->numeric('to_lat')
            ->requirePresence('to_lat', 'create')
            ->notEmptyString('to_lat');

        $validator
            ->numeric('to_lon')
            ->requirePresence('to_lon', 'create')
            ->notEmptyString('to_lon');

        $validator
            ->scalar('to_fulladdress')
            ->maxLength('to_fulladdress', 1024)
            ->allowEmptyString('to_fulladdress');

        $validator
            ->date('from_date')
            ->allowEmptyDate('from_date');

        $validator
            ->date('to_date')
            ->allowEmptyDate('to_date');

        $validator
            ->scalar('ip')
            ->maxLength('ip', 45)
            ->allowEmptyString('ip');

        $validator
            ->scalar('user_agent')
            ->maxLength('user_agent', 1024)
            ->allowEmptyString('user_agent');

        $validator
            ->scalar('type')
            ->maxLength('type', 10)
            ->allowEmptyString('type');

        return $validator;
    }
}
