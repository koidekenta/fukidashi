<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Ipaddress Entity
 *
 * @property int $id
 * @property string $ipaddress
 * @property string $post_slug
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Post $post
 */
class Ipaddress extends Entity
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
        'ipaddress' => true,
        'post_slug' => true,
        'created' => true,
        'modified' => true,
        'post' => true
    ];
}
