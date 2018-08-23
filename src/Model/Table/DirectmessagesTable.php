<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Directmessages Model
 *
 * @method \App\Model\Entity\Directmessage get($primaryKey, $options = [])
 * @method \App\Model\Entity\Directmessage newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Directmessage[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Directmessage|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Directmessage patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Directmessage[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Directmessage findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DirectmessagesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('directmessages');
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
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('from_user')
            ->maxLength('from_user', 255)
            ->requirePresence('from_user', 'create')
            ->notEmpty('from_user');

        $validator
            ->scalar('to_user')
            ->maxLength('to_user', 255)
            ->requirePresence('to_user', 'create')
            ->notEmpty('to_user');

        $validator
            ->scalar('message')
            ->maxLength('message', 255)
            ->requirePresence('message', 'create')
            ->notEmpty('message');

        return $validator;
    }
}
