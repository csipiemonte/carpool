<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Provider Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $url
 * @property string $apikey
 * @property string $privatekey
 * @property string $api
 * @property string $data
 * @property string|null $url_icona
 * @property string|null $homepage
 *
 * @property \App\Model\Entity\SearchResult[] $search_results
 */
class Provider extends Entity
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
        'name' => true,
        'description' => true,
        'url' => true,
        'apikey' => true,
        'privatekey' => true,
        'api' => true,
        'data' => true,
        'url_icona' => true,
        'homepage' => true,
        'search_results' => true,
    ];
}
