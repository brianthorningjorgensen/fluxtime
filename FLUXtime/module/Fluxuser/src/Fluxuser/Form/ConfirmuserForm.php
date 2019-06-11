<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;

class ConfirmuserForm extends Form {

    private $EntityManager;

    public function __construct() {

        // set the name of the form
        parent::__construct('confirmuser');

        // set the method post
        $this->setAttribute('method', 'post');

        // add fields
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
            'name' => 'temppassword',
            'type' => 'password',
            'options' => array(
                'label' => 'Initial password*',
            ),
        ));

        $this->add(array(
            'name' => 'newpassword',
            'type' => 'Password',
            'options' => array(
                'label' => 'New password*',
            ),
            'attributes' => array(
                'id' => 'newpassword',
            ),
        ));

        $this->add(array(
            'name' => 'repeatnewpassword',
            'type' => 'Password',
            'options' => array(
                'label' => 'Confirm new password*',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Confirm',
                'id' => 'submitbutton',
                'class' => 'btn btn-success'
            ),
        ));
    }

    /**
     * Input Filter specification to validate the user
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
            'name' => 'temppassword',
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
        $filter->add(array(
            'name' => 'newpassword',
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
        $filter->add(array(
            'name' => 'repeatnewpassword',
            'required' => true,
            'options' => array(
                'token' => 'newpassword', // name of first password field
            ),
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
