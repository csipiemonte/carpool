<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SearchResults Model
 *
 * @property \App\Model\Table\ProvidersTable&\Cake\ORM\Association\BelongsTo $Providers
 *
 * @method \App\Model\Entity\SearchResult newEmptyEntity()
 * @method \App\Model\Entity\SearchResult newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SearchResult[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SearchResult get($primaryKey, $options = [])
 * @method \App\Model\Entity\SearchResult findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SearchResult patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SearchResult[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SearchResult|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SearchResult saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SearchResult[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SearchResult[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SearchResult[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SearchResult[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SearchResultsTable extends Table
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

        $this->setTable('search_results');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Providers', [
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
            ->scalar('operator')
            ->maxLength('operator', 64)
            ->allowEmptyString('operator');

        $validator
            ->scalar('origin')
            ->maxLength('origin', 128)
            ->allowEmptyString('origin');

        $validator
            ->scalar('logo_supplier')
            ->maxLength('logo_supplier', 64)
            ->allowEmptyString('logo_supplier');

        $validator
            ->scalar('url')
            ->maxLength('url', 256)
            ->allowEmptyString('url');

        $validator
            ->scalar('driver_alias')
            ->maxLength('driver_alias', 64)
            ->allowEmptyString('driver_alias');

        $validator
            ->scalar('driver_image')
            ->maxLength('driver_image', 64)
            ->allowEmptyFile('driver_image');

        $validator
            ->integer('driver_seats')
            ->allowEmptyString('driver_seats');

        $validator
            ->integer('driver_state')
            ->allowEmptyString('driver_state');

        $validator
            ->scalar('route')
            ->maxLength('route', 64)
            ->allowEmptyString('route');

        $validator
            ->numeric('cost_fixed')
            ->allowEmptyString('cost_fixed');

        $validator
            ->numeric('cost_variable')
            ->allowEmptyString('cost_variable');

        $validator
            ->scalar('details')
            ->maxLength('details', 100)
            ->allowEmptyString('details');

        $validator
            ->scalar('vehicle_image')
            ->maxLength('vehicle_image', 100)
            ->allowEmptyFile('vehicle_image');

        $validator
            ->scalar('vehicle_model')
            ->maxLength('vehicle_model', 50)
            ->allowEmptyString('vehicle_model');

        $validator
            ->scalar('vehicle_color')
            ->maxLength('vehicle_color', 50)
            ->allowEmptyString('vehicle_color');

        $validator
            ->scalar('frequency')
            ->maxLength('frequency', 16)
            ->allowEmptyString('frequency');

        $validator
            ->scalar('type')
            ->maxLength('type', 16)
            ->allowEmptyString('type');

        $validator
            ->integer('real_time')
            ->allowEmptyString('real_time');

        $validator
            ->integer('stopped')
            ->allowEmptyString('stopped');

        $validator
            ->integer('mon')
            ->allowEmptyString('mon');

        $validator
            ->integer('tue')
            ->allowEmptyString('tue');

        $validator
            ->integer('wed')
            ->allowEmptyString('wed');

        $validator
            ->integer('thu')
            ->allowEmptyString('thu');

        $validator
            ->integer('fri')
            ->allowEmptyString('fri');

        $validator
            ->integer('sat')
            ->allowEmptyString('sat');

        $validator
            ->integer('sun')
            ->allowEmptyString('sun');

        $validator
            ->date('outward_mindate')
            ->allowEmptyDate('outward_mindate');

        $validator
            ->date('outward_maxdate')
            ->allowEmptyDate('outward_maxdate');

        $validator
            ->time('outward_mon_mintime')
            ->allowEmptyTime('outward_mon_mintime');

        $validator
            ->time('outward_mon_maxtime')
            ->allowEmptyTime('outward_mon_maxtime');

        $validator
            ->time('outward_tue_mintime')
            ->allowEmptyTime('outward_tue_mintime');

        $validator
            ->time('outward_tue_maxtime')
            ->allowEmptyTime('outward_tue_maxtime');

        $validator
            ->time('outward_wed_mintime')
            ->allowEmptyTime('outward_wed_mintime');

        $validator
            ->time('outward_wed_maxtime')
            ->allowEmptyTime('outward_wed_maxtime');

        $validator
            ->time('outward_thu_mintime')
            ->allowEmptyTime('outward_thu_mintime');

        $validator
            ->time('outward_thu_maxtime')
            ->allowEmptyTime('outward_thu_maxtime');

        $validator
            ->time('outward_fri_mintime')
            ->allowEmptyTime('outward_fri_mintime');

        $validator
            ->time('outward_fri_maxtime')
            ->allowEmptyTime('outward_fri_maxtime');

        $validator
            ->time('outward_sat_mintime')
            ->allowEmptyTime('outward_sat_mintime');

        $validator
            ->time('outward_sat_maxtime')
            ->allowEmptyTime('outward_sat_maxtime');

        $validator
            ->time('outward_sun_mintime')
            ->allowEmptyTime('outward_sun_mintime');

        $validator
            ->time('outward_sun_maxtime')
            ->allowEmptyTime('outward_sun_maxtime');

        $validator
            ->date('return_mindate')
            ->allowEmptyDate('return_mindate');

        $validator
            ->date('return_maxdate')
            ->allowEmptyDate('return_maxdate');

        $validator
            ->time('return_mon_mintime')
            ->allowEmptyTime('return_mon_mintime');

        $validator
            ->time('return_mon_maxtime')
            ->allowEmptyTime('return_mon_maxtime');

        $validator
            ->time('return_tue_mintime')
            ->allowEmptyTime('return_tue_mintime');

        $validator
            ->time('return_tue_maxtime')
            ->allowEmptyTime('return_tue_maxtime');

        $validator
            ->time('return_wed_mintime')
            ->allowEmptyTime('return_wed_mintime');

        $validator
            ->time('return_wed_maxtime')
            ->allowEmptyTime('return_wed_maxtime');

        $validator
            ->time('return_thu_mintime')
            ->allowEmptyTime('return_thu_mintime');

        $validator
            ->time('return_thu_maxtime')
            ->allowEmptyTime('return_thu_maxtime');

        $validator
            ->time('return_fri_mintime')
            ->allowEmptyTime('return_fri_mintime');

        $validator
            ->time('return_fri_maxtime')
            ->allowEmptyTime('return_fri_maxtime');

        $validator
            ->time('return_sat_mintime')
            ->allowEmptyTime('return_sat_mintime');

        $validator
            ->time('return_sat_maxtime')
            ->allowEmptyTime('return_sat_maxtime');

        $validator
            ->time('return_sun_mintime')
            ->allowEmptyTime('return_sun_mintime');

        $validator
            ->time('return_sun_maxtime')
            ->allowEmptyTime('return_sun_maxtime');

        $validator
            ->scalar('from_address')
            ->maxLength('from_address', 200)
            ->allowEmptyString('from_address');

        $validator
            ->scalar('from_city')
            ->maxLength('from_city', 50)
            ->allowEmptyString('from_city');

        $validator
            ->numeric('from_latitude')
            ->allowEmptyString('from_latitude');

        $validator
            ->numeric('from_longitude')
            ->allowEmptyString('from_longitude');

        $validator
            ->scalar('to_address')
            ->maxLength('to_address', 200)
            ->allowEmptyString('to_address');

        $validator
            ->scalar('to_city')
            ->maxLength('to_city', 50)
            ->allowEmptyString('to_city');

        $validator
            ->numeric('to_latitude')
            ->allowEmptyString('to_latitude');

        $validator
            ->numeric('to_longitude')
            ->allowEmptyString('to_longitude');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['provider_id'], 'Providers'), ['errorField' => 'provider_id']);

        return $rules;
    }

    /**
     * remove old search results from db (after an hour they're considered as expired)
     */
    public function clearExpired(): int
    {
        return $this->deleteAll(['created <' => date('Y-m-d H:i:s', time() - 3600)]);
    }

    /**
     * remove existing search results for given session from db
     */
    public function clearSession($session_id): int
    {
        return $this->deleteAll(['session_id' => $session_id]);
    }

    public function findDepartureBy(Query $query, array $option): Query
    {
        //$query->func()->sum()
        $departure = "UNIX_TIMESTAMP(outward_mindate) + TIME_TO_SEC(COALESCE(outward_mon_mintime,outward_tue_mintime,outward_wed_mintime,outward_thu_mintime,outward_fri_mintime,outward_sat_mintime,outward_sun_mintime,'00:00:00'))";
        $query = $query
            ->select(['departure' => $departure])
            //->select($this)
            ->andWhere([$departure . '>=' => time()])
            ->enableAutoFields(true);
        //debug($query);
        return $query;
    }

    public function findForRadius(Query $query, array $option): Query
    {
        $lat_from = $option['from']['latitude'];
        $lng_from = $option['from']['longitude'];
        $lat_to = $option['to']['latitude'];
        $lng_to = $option['to']['longitude'];
        $distanceFrom = '(3959 * ACOS(COS(RADIANS(' . $lat_from . ')) * COS(RADIANS(SearchResults.from_latitude)) * COS(RADIANS(SearchResults.from_longitude) - RADIANS(' . $lng_from . ')) + SIN(RADIANS(' . $lat_from . ')) * SIN(RADIANS(SearchResults.from_latitude))))';
        $distanceTo = '(3959*ACOS(COS(RADIANS(' . $lat_to . ')) * COS(RADIANS(SearchResults.to_latitude)) * COS(RADIANS(SearchResults.to_longitude) - RADIANS(' . $lng_to . ')) + SIN(RADIANS(' . $lat_to . ')) * SIN(RADIANS(SearchResults.to_latitude))))';

        $query = $query
            ->select(['distance_from' => $distanceFrom, 'distance_to' => $distanceTo])
            //->select($this)
            ->andWhere([$distanceFrom . '<' => (int)$option['radius'] * 0.621371192]) // km to miles
            ->andWhere([$distanceTo . '<' => (int)$option['radius'] * 0.621371192]); // km to miles
        return $query;
    }

    public function findForTime(Query $query, array $option): Query
    {
        $mintime = (strlen(strval($option['outward']['mintime'])) == 1 ? '0' : '') . $option['outward']['mintime'] . ':00:00';
        $conditions[] = [
            'OR' => [
                'outward_mon_mintime >=' => $mintime,
                'outward_tue_mintime >=' => $mintime,
                'outward_wed_mintime >=' => $mintime,
                'outward_thu_mintime >=' => $mintime,
                'outward_fri_mintime >=' => $mintime,
                'outward_sat_mintime >=' => $mintime,
                'outward_sun_mintime >=' => $mintime
            ]
        ];
        // maxtime
        $maxtime = (strlen(strval($option['outward']['maxtime'])) == 1 ? '0' : '') . $option['outward']['maxtime'] . ':00:00';
        $conditions[] = [
            'OR' => [
                'outward_mon_maxtime <=' => $maxtime,
                'outward_tue_maxtime <=' => $maxtime,
                'outward_wed_maxtime <=' => $maxtime,
                'outward_thu_maxtime <=' => $maxtime,
                'outward_fri_maxtime <=' => $maxtime,
                'outward_sat_maxtime <=' => $maxtime,
                'outward_sun_maxtime <=' => $maxtime
            ]
        ];
        $query = $query->andWhere([$conditions]);
        return $query;
    }

    public function findComplexFields(Query $query, array $option): Query
    {
        //$query = $query->where()
        $ricalcolo = $option['ricalcolo'];
        $criteria = $option['criteria'];
        $session_id = $option['session_id'];
        if ($ricalcolo && !empty($criteria['radius'])) {
            $query = $this->findForRadius($query, $criteria);
        }
        if($ricalcolo && !empty($criteria['outward'])){
            $query = $this->findForTime($query, $criteria);
        }
        $query = $query
            ->select($this)
            ->andWhere(['SearchResults.session_id' => $session_id]);
        //debug($query);
        return $this->findDepartureBy($query, $option);
    }
}
