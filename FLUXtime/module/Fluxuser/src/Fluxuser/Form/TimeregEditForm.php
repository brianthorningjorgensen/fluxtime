<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

//Edit og Create project
class TimeregEditForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {
        $this->EntityManager = $entityManager;
        parent::__construct('timeregform');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');

        //Text field's

        // from date
        $this->add(array(
            'name' => 'from',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'from',
            ),
            'options' => array(
                'label' => 'From date',
            ),
        ));

        // to date
        $this->add(array(
            'name' => 'to',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'to',               
            ),
            'options' => array(
                'label' => 'To date',
            ),
        ));
        
        //Save button
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Create new Time registration',
                'id' => 'submitbutton',
                'class' => 'btn btn-success'
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
            'name' => 'from',
            'required' => true,
        ));

        $filter->add(array(
            'name' => 'to',
            'required' => true,
        ));

        return $filter;
    }

}
