<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class AccountForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {

        $this->EntityManager = $entityManager;

        parent::__construct('account');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');

        //Text field's 
        $this->add(array(
            'name' => 'customer',
            'type' => 'Text',
            'attributes' =>
            array(
                'id' => 'companyname',
            ),
            'options' =>
            array(
                'label' => 'Account*',
            ),
        ));

        $this->add(array(
            'name' => 'customerid',
            'type' => 'Text',
            'attributes' =>
            array(
                'id' => 'compId',
            ),
            'options' =>
            array(
                'label' => 'Company ID',
            ),
        ));




        $this->add(array(
            'name' => 'username',
            'type' => 'Text',
            'attributes' =>
            array(
                'id' => 'uid',
            ),
            'options' =>
            array(
                'label' => 'Admin username*',
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
            'name' => 'employeeId',
            'type' => 'Text',
            'attributes' =>
            array(
                'id' => 'impid',
            ),
            'options' =>
            array(
                'label' => 'Employee ID',
            ),
        ));

        $this->add(array(
            'name' => 'firstname',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'First name*',
            ),
        ));

        $this->add(array(
            'name' => 'lastname',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'Last name*',
            ),
        ));

        $this->add(array(
            'name' => 'workEmail',
            'type' => 'Text',
            'attributes' =>
            array(
                'id' => 'weid',
            ),
            'options' =>
            array(
                'label' => 'Email*',
            ),
        ));

        $this->add(array(
            'name' => 'phone',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'Phone',
            ),
        ));

        $this->add(array(
            'name' => 'street',
            'type' => 'Text',
            'options' => array(
                'label' => 'Street',
            ),
        ));

        $this->add(array(
            'name' => 'houseNumber',
            'type' => 'Text',
            'options' => array(
                'label' => 'House number',
            ),
        ));

        $this->add(array(
            'name' => 'city',
            'type' => 'Text',
            'options' => array(
                'label' => 'City',
            ),
        ));

        $this->add(array(
            'name' => 'zipCode',
            'type' => 'Text',
            'options' => array(
                'label' => 'Zip code',
            ),
        ));

        $this->add(array(
            'name' => 'country',
            'type' => 'Text',
            'options' => array(
                'label' => 'Country',
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
            'name' => 'phonePrivate',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'Private phone',
            ),
        ));

       
        
        
        $this->add(array(
            'name' => 'client',
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'options' => array(
                'object_manager' => $entityManager,
                'target_class' => 'Fluxuser\Entity\Client',
                'label' => 'Client',
            ),
            'attributes' => array(
                'id' => 'client',
            ),
        ));
        
         $this->add(array(
            'name' => 'fkuserrole',
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'options' => array(
                'object_manager' => $entityManager,
                'target_class' => 'Fluxuser\Entity\Usergroup',
                'label' => 'Permissions*',
            ),
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'Textarea',
            'options' =>
            array(
                'label' => 'Comment',
            ),
        ));

        $this->add(array(
            'name' => 'active',
            'type' => 'checkbox',
            'attributes' => array(
                'id' => 'active',
                'title' => "Account users can only login, if account is active",
            ),
            'options' => array(
                'checked_value' => 1,
                'unchecked_value' => 0,
                'label' => 'Account active',
            ),
        ));

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'accountid',
            'type' => 'Hidden',
        ));



        //Button
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save account',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
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
            'name' => 'customer',
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
                        'max' => 100,
                        'message' => 'Max characters ' . '100'
                    ),
                ),
            ),
        ));
        $filter->add(array(
            'name' => 'customerid',
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
            'name' => 'username',
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
                        'min' => 2,
                        'max' => 20,
                        'message' => 'Characters between ' . '2-20'
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
            'name' => 'firstname',
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
                        'max' => 40,
                        'message' => 'Max characters ' . '40'
                    ),
                ),
            ),
        ));
        $filter->add(array(
            'name' => 'lastname',
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
                        'max' => 40,
                        'message' => 'Max characters ' . '40'
                    ),
                ),
            ),
        ));
        $filter->add(array(
            'name' => 'employeeId',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'max' => 30,
                        'message' => 'Max characters ' . '30'
                    ),
                ),
            ),
        ));
        $filter->add(array(
            'name' => 'street',
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
            'name' => 'phone',
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
        $filter->add(array(
            'name' => 'houseNumber',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'max' => 10,
                        'message' => 'Max characters ' . '10'
                    ),
                ),
            ),
        ));
        $filter->add(array(
            'name' => 'city',
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
            'name' => 'zipCode',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'max' => 10,
                        'message' => 'Max characters ' . '10'
                    ),
                ),
            ),
        ));
        $filter->add(array(
            'name' => 'country',
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
