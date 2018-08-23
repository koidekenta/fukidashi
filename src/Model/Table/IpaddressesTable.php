<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Ipaddresses Model
 *
 * @method \App\Model\Entity\Ipaddress get($primaryKey, $options = [])
 * @method \App\Model\Entity\Ipaddress newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Ipaddress[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Ipaddress|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Ipaddress patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Ipaddress[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Ipaddress findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class IpaddressesTable extends Table
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

        $this->setTable('ipaddresses');
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
            ->scalar('ipaddress')
            ->maxLength('ipaddress', 150)
            ->requirePresence('ipaddress', 'create')
            ->notEmpty('ipaddress');

        $validator
            ->scalar('post_slug')
            ->maxLength('post_slug', 150)
            ->requirePresence('post_slug', 'create')
            ->notEmpty('post_slug');

        return $validator;
    }
}
