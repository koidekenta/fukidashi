<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Post Entity
 *
 * @property int $id
 * @property string $post
 * @property int $user_id
 * @property string $slug
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Comment[] $comments
 */
class Post extends Entity
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
        'post' => true,
        'user_id' => true,
        'slug' => true,
	'username' => true,
	'post_img' => true,
        'created' => true,
        'modified' => true,
	'dummy_column' => true,
	'is_retweeted' => true,
	'is_commented' => true,
	'retweet_slug' => true,
	'comment_slug' => true,
        'user' => true,
        'comments' => true
    ];
}
