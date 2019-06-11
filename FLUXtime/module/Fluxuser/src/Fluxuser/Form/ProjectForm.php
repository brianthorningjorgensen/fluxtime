<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

//Edit og Create project
class ProjectForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {

        $this->EntityManager = $entityManager;

        parent::__construct('project');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');

        //Text field's
        $this->add(array(
            'name' => 'projectname',
            'type' => 'Text',
            'attributes' => array(          
                'id'    => 'projectnameid',
            ),
            
            'options' => array(               
                'label' => 'Project name*',
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
                'id'    => 'client',
            ),
        ));
        
         $this->add(array(
            'name' => 'active',
            'type' => 'checkbox',
            'attributes' => array(          
                'id'    => 'active',
            ),
            'options' => array(
                   'checked_value' => 1,
                   'unchecked_value' => 0,                  
                    'label' => 'Active',                    
            ),
        ));

        $this->add(array(
            'name' => 'fkProjectmanager',
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'options' => array(
                'object_manager' => $entityManager,
                'target_class' => 'Fluxuser\Entity\FluxUser',
                'label' => 'Project manager',
            ),
        ));
        
         $this->add(array(
            'name' => 'secondid',
            'type' => 'Text', 
            'options' => array(               
                'label' => 'Import id*',
            ),
             'attributes' => array(          
                'disabled'    => 'disabled',
            ),
        ));


        //Hidden id field - edit
        $this->add(array(
            'name' => 'projectid',
            'type' => 'Hidden',
        ));
     

        //Save button
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save project',
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
            'name' => 'projectname',
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
                        'max' => 50,
                        'message' => 'Max characters' . '50'
                    ),
                ),
            ),
        ));

    

        return $filter;
    }

}
