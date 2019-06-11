<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class ProfileForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {

        $this->EntityManager = $entityManager;

        parent::__construct('fluxuser');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');

        //Text field's
        $this->add(array(
            'name' => 'oldpassword',
            'type' => 'Password',
            'options' => array(
                'label' => 'Password*',
            ),
        ));
        
        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'options' => array(
                'label' => 'New password',
            ),
        ));

        //Disable
        $this->add(array(
            'name' => 'username',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'Username',
            ),
        ));

        //Disable
        $this->add(array(
            'name' => 'firstname',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'First name',
            ),
        ));

        //Disable
        $this->add(array(
            'name' => 'lastname',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'Last name',
            ),
        ));

        $this->add(array(
            'name' => 'phonePrivate',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'Private phone',
            ),
        ));

        $this->add(array(
            'name' => 'privateEmail',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'Private email',
            ),
        ));

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        //Button
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save profile',
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
            'name' => 'oldpassword',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 8,
                        'max' => 72,
                        'message' => "Between characters " . '8-32'
                    ),
                ),
            ),
        ));
        
        $filter->add(array(
            'name' => 'password',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 8,
                        'max' => 72,
                        'message' => "Between characters" . '8-32'
                    ),
              ),
                ),
            )
        );

        $filter->add(array(
            'name' => 'privateEmail',
            'required' => false,
            'validators' => array(
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
            'name' => 'phonePrivate',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'max' => 20,
                        'message' => 'Max characters ' . '20'
                    ),
                ),
            ),
        ));
        return $filter;
    }

}
