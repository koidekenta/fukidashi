<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Alerts Model
 *
 * @method \App\Model\Entity\Alert get($primaryKey, $options = [])
 * @method \App\Model\Entity\Alert newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Alert[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Alert|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Alert patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Alert[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Alert findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AlertsTable extends Table
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

        $this->setTable('alerts');
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
            ->scalar('who')
            ->maxLength('who', 150)
            ->requirePresence('who', 'create')
            ->notEmpty('who');

        $validator
            ->scalar('action')
            ->requirePresence('action', 'create')
            ->notEmpty('action');

        $validator
            ->scalar('post_slug')
            ->maxLength('post_slug', 150)
            ->allowEmpty('post_slug');

        return $validator;
    }
}
