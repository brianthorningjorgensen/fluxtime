<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

//Edit og Create project
class ProjectTaskForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {
        $this->EntityManager = $entityManager;
        parent::__construct('projecttask');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');

        //Text field's


        $this->add(array(
            'name' => 'taskname',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'taskname',
            ),
            'options' => array(
                'label' => 'Task*',
            ),
        ));

        // label 
        $this->add(array(
            'name' => 'fklabelid',
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'attributes' => array(
                'id' => 'fklabelid',
            ),
            'options' => array(
                'object_manager' => $entityManager,
                'target_class' => 'Fluxuser\Entity\Projectlabel',
                'label' => 'Label',
            ),
        ));



        // points
        $this->add(array(
            'name' => 'points',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'points',
            ),
            'options' => array(
                'label' => 'Points',
            ),
        ));

        // description
        $this->add(array(
            'name' => 'description',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'description',
            ),
            'options' => array(
                'label' => 'Description',
            ),
        ));


        $this->add(array(
            'name' => 'tasktype',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'tasktype',
            ),
            'options' => array(
                'label' => 'Task type'
            ),
        ));

        $this->add(array(
            'name' => 'status',
            'type' => 'Select',
            'options' => array(
                'label' => 'Status*',
                'value_options' => array(
                    'accepted' => 'accepted',
                    'delivered' => 'delivered',
                    'finished' => 'finished',
                    'started' => 'started',
                    'unstarted' => 'unstarted',
                    'planned' => 'planned',
                    'rejected' => 'rejected',
                    'unscheduled' => 'unscheduled',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'secondid',
            'type' => 'Text',
            'options' => array(
                'label' => 'Import ID*'
            ),
            'attributes' => array(
                'disabled' => 'disabled',
            ),
        ));

        $this->add(array(
            'name' => 'fkCreator',
            'type' => 'Text',
            'options' => array(
                'label' => 'Creator'
            ),
            'attributes' => array(
                'disabled' => 'disabled',
            ),
        ));


        //Hidden id field 
        $this->add(array(
            'name' => 'taskid',
            'type' => 'Hidden',
        ));

        //Save button
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save task',
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
            'name' => 'taskname',
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
                        'max' => 255,
                        'message' => "Max characters " . '255'
                    ),
                ),
            ),
        ));

        $filter->add(array(
            'name' => 'tasktype',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(                 
                        'max' => 30,                        
                        'message' => "Max characters" . '30'
                    ),
                ),
            ),
        ));
        
         $filter->add(array(
            'name' => 'points',
            'required' => false,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(                 
                        'max' => 30,                        
                        'message' => "Max characters" . '30'
                    ),
                ),
            ),
        ));

        $filter->add(array(
            'name' => 'fklabelid',
            'required' => false,
        ));
        
         $filter->add(array(
            'name' => 'status',
            'required' => false,
        ));

        return $filter;
    }

}
