<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * WidgetTrackings Model
 *
 * @method \App\Model\Entity\WidgetTracking newEmptyEntity()
 * @method \App\Model\Entity\WidgetTracking newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\WidgetTracking[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\WidgetTracking get($primaryKey, $options = [])
 * @method \App\Model\Entity\WidgetTracking findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\WidgetTracking patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\WidgetTracking[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\WidgetTracking|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\WidgetTracking saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\WidgetTracking[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\WidgetTracking[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\WidgetTracking[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\WidgetTracking[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class WidgetTrackingsTable extends Table
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

        $this->setTable('widget_trackings');
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
            ->requirePresence('id', 'create')
            ->notEmptyString('id');

        $validator
            ->scalar('url')
            ->maxLength('url', 200)
            ->requirePresence('url', 'create')
            ->notEmptyString('url');

        $validator
            ->integer('hits_num')
            ->requirePresence('hits_num', 'create')
            ->notEmptyString('hits_num');

        return $validator;
    }
}
