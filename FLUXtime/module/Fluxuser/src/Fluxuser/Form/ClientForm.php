<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class ClientForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {

        $this->EntityManager = $entityManager;

        parent::__construct('client');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');

        //Text field's      
        $this->add(array(
            'name' => 'clientname',
            'type' => 'Text',
            'attributes' =>
            array(
                'id' => 'cliname',
            ),
            'options' =>
            array(
                'label' => 'Client*',
            ),
        ));

        
        $this->add(array(
            'name' => 'cvrid',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'Client id',
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
            'name' => 'zipCode',
            'type' => 'Text',
            'options' => array(
                'label' => 'Zip code',
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
            'name' => 'country',
            'type' => 'Text',
            'options' => array(
                'label' => 'Country',
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
            'name' => 'email',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'Email',
            ),
        ));

       

        $this->add(array(
            'name' => 'clientid',
            'type' => 'Hidden',
        ));

        //Button
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save client',
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
            'name' => 'clientname',
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
        )));
        $filter->add(array(
            'name' => 'cvrid',
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
            'name' => 'email',
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
       
        return $filter;
    }

}
