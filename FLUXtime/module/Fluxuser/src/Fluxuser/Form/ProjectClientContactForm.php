<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

//Edit og Create project
class ProjectClientContactForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {

        $this->EntityManager = $entityManager;

        parent::__construct('projectclientcontact');

        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'fkcontactid',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'selectedclient',
                'class' => 'clientcontactselect'
            ),
             'options' => array(               
                'label' => 'Client contact*',
            ),
        ));
        
       
        //Hidden id field - edit
        $this->add(array(
            'name' => 'fkProjectid',
            'type' => 'Hidden',
        ));

        
    }

    /**
     * Validering af input 
     * @return InputFilter
     */
    public function getInputFilterSpecification() {
        $filter = new InputFilter();

        $filter->add(array(
            'name' => 'fkcontactid',
            'required' => true,
        ));

          $filter->add(array(
            'name' => 'fkProjectid',
            'required' => true,
        ));
          
        return $filter;
    }

}




