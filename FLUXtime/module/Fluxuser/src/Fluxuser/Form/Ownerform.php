<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

//Edit og Create label - en label kan kategorisere opgaver/tasks
class OwnerForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {

        $this->EntityManager = $entityManager;

        parent::__construct('owner');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');

        $this->add(array(
            'name' => 'fkuserid',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'ownerid',
            ),
             'options' => array(               
                'label' => 'Owner*',
            ),
        ));
        
        $this->add(array(
            'name' => 'fktaskid',
            'type' => 'hidden',
            'attributes' => array(
                'id' => 'taskid',
            ),
        ));
        
    }

    /**
     * Validering af input 
     * @return InputFilter
     */
    public function getInputFilterSpecification() {
        $filter = new InputFilter();
        //Intet filter - vil altid være valid
        return $filter;
    }

}
