<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class LoginForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {

        $this->EntityManager = $entityManager;

        // we want to ignore the name passed
        parent::__construct('fluxuser');

        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'workEmail',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'Email*',
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'options' => array(
                'label' => 'Password*',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'add user',
                'id' => 'submitbutton',
            ),
        ));
    }

    /**
     * Validation rules
     * @return type
     */
    public function getInputFilterSpecification() {
          $filter = new InputFilter();

         $filter->add(array(
            'name' => 'workEmail',
            'required' => TRUE,
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
                        'min' => 8,
                        'message' => 'Characters between ' . '8 - 50'
                    ),
                ),
                array(
                    'name' => 'EmailAddress',
                    'options' => array(
                        'mx' => 'true',
                        'deep' => 'true',
                        'message' => "Invalid email format",
                        'domain' => 'true',
                        'hostname' => 'true',
                    ),
                ),
            ),
        ));
        $filter->add(array(
            'name' => 'password',
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
                        'min' => 8,
                        'max' => 32,
                        'message' => 'Characters between ' . '8-32'
                    ),
                ),
            ),
        ));
       
        return $filter;
    }

}
