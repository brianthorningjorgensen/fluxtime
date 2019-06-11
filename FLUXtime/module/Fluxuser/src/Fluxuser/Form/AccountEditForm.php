<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class AccountEditForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {

        $this->EntityManager = $entityManager;

        parent::__construct('accountEdit');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');

        //Text field's 
         $this->add(array(
            'name' => 'customer',
            'type' => 'Text',
            'attributes' => 
            array (
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
            array (
            'id' => 'compId',
            ),
            'options' =>
            array(
                'label' => 'Company ID',
            ),
        ));
          
        
              $this->add(array(
            'name' => 'description',
            'type' => 'Textarea',
            'options' =>
            array(
                'label' => 'Comment',
            ),
                  'attributes' => array(          
                'id'    => 'comment',
            ),
        ));
        
         $this->add(array(
            'name' => 'active',
            'type' => 'checkbox',
            'attributes' => array(          
                'id'    => 'active',
                'title' => "Account users can only login, if account is active",
            ),
            'options' => array(
                   'checked_value' => 1,
                   'unchecked_value' => 0,                  
                    'label' => 'Account active',                    
            ), 
        ));
         
           $this->add(array(
            'name' => 'clientid',
            'type' => 'Hidden',
                 'attributes' => array(          
                'id'    => 'clientId',
            ),
        ));
           
              $this->add(array(
            'name' => 'clientname',
            'type' => 'Hidden',
                 'attributes' => array(          
                'id'    => 'client',
            ),
        ));
        
         $this->add(array(
            'name' => 'accountid',
            'type' => 'Hidden',
                 'attributes' => array(          
                'id'    => 'accId',
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
        

        return $filter;
    }

}
