<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

//Edit og Create project
class ProjectmemberForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {

        $this->EntityManager = $entityManager;

        parent::__construct('projectmember');

        $this->setAttribute('method', 'post');


        
        $this->add(array(
            'name' => 'fkUserid',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'id' => 'selecteduser',
                'class' => 'memberselect'
            ),
             'options' => array(               
                'label' => 'User*',
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
            'name' => 'fkUserid',
            'required' => true,
        ));

          $filter->add(array(
            'name' => 'fkProjectid',
            'required' => true,
        ));
          
        return $filter;
    }

}




