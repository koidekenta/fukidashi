<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Directmessage Entity
 *
 * @property int $id
 * @property string $from_user
 * @property string $to_user
 * @property string $message
 * @property \Cake\I18n\FrozenTime $created
 */
class Directmessage extends Entity
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
        'from_user' => true,
        'to_user' => true,
        'message' => true,
        'created' => true
    ];
}
