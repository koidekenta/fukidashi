<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Alert Entity
 *
 * @property int $id
 * @property string $who
 * @property string $action
 * @property string $post_slug
 * @property \Cake\I18n\FrozenTime $created
 */
class Alert extends Entity
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
        'who' => true,
        'action' => true,
        'post_slug' => true,
	'flug' => true,
        'created' => true
    ];
}
