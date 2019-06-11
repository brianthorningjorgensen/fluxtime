<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

//Edit og Create project
class TimeregForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {
        $this->EntityManager = $entityManager;
        parent::__construct('timeregform');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');

        //Text field's

        // users
        $this->add(array(
            'name' => 'users',
            'type' => 'select',
            'attributes' => array(
                'id' => 'users',
            ),
            'options' => array(
                'label' => 'User*',
                 'disable_inarray_validator' => true,
            ),
        ));

        // projects
        $this->add(array(
            'name' => 'projects',
            'type' => 'select',
            'attributes' => array(
                'id' => 'projects',
            ),
            'options' => array(
                'label' => 'Active project*',
                 'disable_inarray_validator' => true,
                
            ),
        ));
        
        // labels
        $this->add(array(
            'name' => 'labels',
            'type' => 'select',
            'attributes' => array(
                'id' => 'labels',
                 'disable_inarray_validator' => true,
            ),
            'options' => array(               
                'label' => 'Label*',
                 'disable_inarray_validator' => true,
            ),
        ));
        
        // tasks
        $this->add(array(
            'name' => 'tasks',
            'type' => 'select',
            'attributes' => array(
                'id' => 'tasks',
            ),
            'options' => array(               
                'label' => 'Task*',
                 'disable_inarray_validator' => true,
            ),
        ));

        // from date
        $this->add(array(
            'name' => 'from',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'from',
            ),
            'options' => array(
                'label' => 'From*',
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
                'label' => 'To*',
            ),
        ));
        
         //Hidden id field - edit
        $this->add(array(
            'name' => 'projectid',
            'type' => 'Hidden',
             'attributes' => array(
                'id' => 'pid',
            ),
        ));

        //Save button
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save time registration',
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
            'name' => 'users',
            'required' => true,
        ));
        
         $filter->add(array(
            'name' => 'projects',
            'required' => true,
        ));

        $filter->add(array(
            'name' => 'labels',
            'required' => true,
        ));

        $filter->add(array(
            'name' => 'tasks',
            'required' => true,
        ));

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
