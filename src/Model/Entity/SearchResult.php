<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SearchResult Entity
 *
 * @property int $id
 * @property string|null $operator
 * @property string|null $origin
 * @property string|null $logo_supplier
 * @property string|null $url
 * @property string|null $driver_id
 * @property string|null $driver_alias
 * @property string|null $driver_image
 * @property int|null $driver_seats
 * @property int|null $driver_state
 * @property string|null $route
 * @property float|null $cost_fixed
 * @property float|null $cost_variable
 * @property string|null $details
 * @property string|null $vehicle_image
 * @property string|null $vehicle_model
 * @property string|null $vehicle_color
 * @property string|null $frequency
 * @property string|null $type
 * @property int|null $real_time
 * @property int|null $stopped
 * @property int|null $mon
 * @property int|null $tue
 * @property int|null $wed
 * @property int|null $thu
 * @property int|null $fri
 * @property int|null $sat
 * @property int|null $sun
 * @property \Cake\I18n\FrozenDate|null $outward_mindate
 * @property \Cake\I18n\FrozenDate|null $outward_maxdate
 * @property \Cake\I18n\FrozenTime|null $outward_mon_mintime
 * @property \Cake\I18n\FrozenTime|null $outward_mon_maxtime
 * @property \Cake\I18n\FrozenTime|null $outward_tue_mintime
 * @property \Cake\I18n\FrozenTime|null $outward_tue_maxtime
 * @property \Cake\I18n\FrozenTime|null $outward_wed_mintime
 * @property \Cake\I18n\FrozenTime|null $outward_wed_maxtime
 * @property \Cake\I18n\FrozenTime|null $outward_thu_mintime
 * @property \Cake\I18n\FrozenTime|null $outward_thu_maxtime
 * @property \Cake\I18n\FrozenTime|null $outward_fri_mintime
 * @property \Cake\I18n\FrozenTime|null $outward_fri_maxtime
 * @property \Cake\I18n\FrozenTime|null $outward_sat_mintime
 * @property \Cake\I18n\FrozenTime|null $outward_sat_maxtime
 * @property \Cake\I18n\FrozenTime|null $outward_sun_mintime
 * @property \Cake\I18n\FrozenTime|null $outward_sun_maxtime
 * @property \Cake\I18n\FrozenDate|null $return_mindate
 * @property \Cake\I18n\FrozenDate|null $return_maxdate
 * @property \Cake\I18n\FrozenTime|null $return_mon_mintime
 * @property \Cake\I18n\FrozenTime|null $return_mon_maxtime
 * @property \Cake\I18n\FrozenTime|null $return_tue_mintime
 * @property \Cake\I18n\FrozenTime|null $return_tue_maxtime
 * @property \Cake\I18n\FrozenTime|null $return_wed_mintime
 * @property \Cake\I18n\FrozenTime|null $return_wed_maxtime
 * @property \Cake\I18n\FrozenTime|null $return_thu_mintime
 * @property \Cake\I18n\FrozenTime|null $return_thu_maxtime
 * @property \Cake\I18n\FrozenTime|null $return_fri_mintime
 * @property \Cake\I18n\FrozenTime|null $return_fri_maxtime
 * @property \Cake\I18n\FrozenTime|null $return_sat_mintime
 * @property \Cake\I18n\FrozenTime|null $return_sat_maxtime
 * @property \Cake\I18n\FrozenTime|null $return_sun_mintime
 * @property \Cake\I18n\FrozenTime|null $return_sun_maxtime
 * @property string|null $from_address
 * @property string|null $from_city
 * @property float|null $from_latitude
 * @property float|null $from_longitude
 * @property string|null $to_address
 * @property string|null $to_city
 * @property float|null $to_latitude
 * @property float|null $to_longitude
 * @property string $session_id
 * @property int|null $provider_id
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \App\Model\Entity\Provider $provider
 */
class SearchResult extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'operator' => true,
        'origin' => true,
        'logo_supplier' => true,
        'url' => true,
        'driver_id' => true,
        'driver_alias' => true,
        'driver_image' => true,
        'driver_seats' => true,
        'driver_state' => true,
        'route' => true,
        'cost_fixed' => true,
        'cost_variable' => true,
        'details' => true,
        'vehicle_image' => true,
        'vehicle_model' => true,
        'vehicle_color' => true,
        'frequency' => true,
        'type' => true,
        'real_time' => true,
        'stopped' => true,
        'mon' => true,
        'tue' => true,
        'wed' => true,
        'thu' => true,
        'fri' => true,
        'sat' => true,
        'sun' => true,
        'outward_mindate' => true,
        'outward_maxdate' => true,
        'outward_mon_mintime' => true,
        'outward_mon_maxtime' => true,
        'outward_tue_mintime' => true,
        'outward_tue_maxtime' => true,
        'outward_wed_mintime' => true,
        'outward_wed_maxtime' => true,
        'outward_thu_mintime' => true,
        'outward_thu_maxtime' => true,
        'outward_fri_mintime' => true,
        'outward_fri_maxtime' => true,
        'outward_sat_mintime' => true,
        'outward_sat_maxtime' => true,
        'outward_sun_mintime' => true,
        'outward_sun_maxtime' => true,
        'return_mindate' => true,
        'return_maxdate' => true,
        'return_mon_mintime' => true,
        'return_mon_maxtime' => true,
        'return_tue_mintime' => true,
        'return_tue_maxtime' => true,
        'return_wed_mintime' => true,
        'return_wed_maxtime' => true,
        'return_thu_mintime' => true,
        'return_thu_maxtime' => true,
        'return_fri_mintime' => true,
        'return_fri_maxtime' => true,
        'return_sat_mintime' => true,
        'return_sat_maxtime' => true,
        'return_sun_mintime' => true,
        'return_sun_maxtime' => true,
        'from_address' => true,
        'from_city' => true,
        'from_latitude' => true,
        'from_longitude' => true,
        'to_address' => true,
        'to_city' => true,
        'to_latitude' => true,
        'to_longitude' => true,
        'session_id' => true,
        'provider_id' => true,
        'created' => true,
        'driver' => true,
        'session' => true,
        'provider' => true,
        'departure' => true
    ];
}
