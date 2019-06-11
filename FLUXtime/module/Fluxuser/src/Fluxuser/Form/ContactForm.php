<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class ContactForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {

        $this->EntityManager = $entityManager;

        parent::__construct('contactform');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form');

        //Text field's      
        $this->add(array(
            'name' => 'firstname',
            'type' => 'Text',
            'attributes' =>
            array(
                'id' => 'firstname',
            ),
            'options' =>
            array(
                'label' => 'Firstname*',
            ),
        ));

        //Text field's      
        $this->add(array(
            'name' => 'lastname',
            'type' => 'Text',
            'attributes' =>
            array(
                'id' => 'lastname',
            ),
            'options' =>
            array(
                'label' => 'Lastname*',
            ),
        ));

        $this->add(array(
            'name' => 'phone',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'Phone*',
            ),
            'attributes' =>
            array(
                'id' => 'phone',
            ),
        ));
        
        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'Email*',
            ),
            'attributes' =>
            array(
                'id' => 'email',
            ),
        ));
        
        $this->add(array(
            'name' => 'description',
            'type' => 'Text',
            'options' =>
            array(
                'label' => 'Description',
            ),
            'attributes' =>
            array(
                'id' => 'description',
            ),
        ));

        $this->add(array(
            'name' => 'contactid',
            'type' => 'Hidden',
             'attributes' =>
            array(
                'id' => 'contactid',
            ),
        ));

        $this->add(array(
            'name' => 'clientid',
            'type' => 'Hidden',
             'attributes' =>
            array(
                'id' => 'clientid',
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
        )));
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
        )));
        
       $filter->add(array(
            'name' => 'email',
            'required' => true,
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
            'name' => 'clientid',
            'required' => true
            ));
        
        return $filter;
    }

}
