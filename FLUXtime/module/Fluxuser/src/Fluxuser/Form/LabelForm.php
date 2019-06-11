<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

//Edit og Create label - en label kan kategorisere opgaver/tasks
class LabelForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {

        $this->EntityManager = $entityManager;

        parent::__construct('label');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form');

        //Text field's       
        $this->add(array(
            'name' => 'labelname',
            'type' => 'Text',
            'options' => array(
                'label' => 'Label*',
            ),
            'attributes' => array(
                'id' => 'labeltext',
            ),
        ));

        //Hidden project- og label id's
        $this->add(array(
            'name' => 'fkProjectid',
            'type' => 'Hidden',
            'attributes' => array(
                'id' => 'projectId',
            ),
        ));

        $this->add(array(
            'name' => 'labelid',
            'type' => 'Hidden',
            'attributes' => array(
                'id' => 'labelId',
            ),
        ));
    }

    /**
     * Validering af input 
     * @return InputFilter
     */
    public function getInputFilterSpecification() {
        $filter = new InputFilter();

        $filter->add(array(
            'name' => 'labelname',
            'required' => true,
               'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'message' => 'Required field'
                    ),
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'max' => 50,
                        'message' => 'Max characters ' . '50'
                    ),
                ),
            ),
        ));

        $filter->add(array(
            'name' => 'fkProjectid',
            'required' => true,
        ));

        $filter->add(array(
            'name' => 'labelid',
            'required' => false,
        ));


        return $filter;
    }

}
