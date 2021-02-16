<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SearchRequest Entity
 *
 * @property int $id
 * @property float $from_lat
 * @property float $from_lon
 * @property string|null $from_fulladdress
 * @property float $to_lat
 * @property float $to_lon
 * @property string|null $to_fulladdress
 * @property \Cake\I18n\FrozenDate|null $from_date
 * @property \Cake\I18n\FrozenDate|null $to_date
 * @property \Cake\I18n\FrozenTime $created
 * @property string|null $ip
 * @property string|null $user_agent
 * @property string|null $type
 * @property int|null $seats
 */
class SearchRequest extends Entity
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
        'from_lat' => true,
        'from_lon' => true,
        'from_fulladdress' => true,
        'to_lat' => true,
        'to_lon' => true,
        'to_fulladdress' => true,
        'from_date' => true,
        'to_date' => true,
        'created' => true,
        'ip' => true,
        'user_agent' => true,
        'type' => true,
        'seats' => true
    ];
}
