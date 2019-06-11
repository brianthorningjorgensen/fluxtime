<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;

class ConfirmResetPasswordForm extends Form {

    public function __construct() {

        // set the name of the form
        parent::__construct('confirmresetpasswordform');

        // set the method post
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');

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
                'value' => 'Reset password',
                'id' => 'submitbutton',
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
