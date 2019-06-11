<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class EditFluxuserForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {

        $this->EntityManager = $entityManager;

        parent::__construct('fluxuser');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');

        //Text field's     
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
            'name' => 'username',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'Username*',
            ),
            'attributes' =>
            array(
                'readonly' => 'readonly'
            )
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
            'options' =>
            array(
                'label' => 'Email*',
            ),
            'attributes' =>
            array(
                'readonly' => 'readonly'
            )
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
            'name' => 'fkuserrole',
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'options' => array(
                'object_manager' => $entityManager,
                'target_class' => 'Fluxuser\Entity\Usergroup',
                'label' => 'Permissions*',
            ),
        ));

        $this->add(array(
            'name' => 'pivotaltrackerapi',
            'type' => 'Text',
            'options' => array(
                'label' => 'Pivotal Tracker API Token',
            ),
            'attributes' => array(                
                'readonly' => 'readonly',
                'id' => 'api',
            ),
        ));

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
            'attributes' =>
            array(
                'id' => 'userid',
            ),
        ));
        

        //Button
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save user',
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
