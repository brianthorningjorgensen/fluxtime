<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

//Edit og Create project
class ReportForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {

        $this->EntityManager = $entityManager;

        parent::__construct('report');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');

        //method selector
        $this->add(array(
            'name' => 'choices',
            'type' => 'select',
            'attributes' => array(          
                'id'    => 'choices',
            ),
            
            'options' => array(               
                'label' => 'Limit choices*',
                'disable_inarray_validator' => true,
            ),
        ));
        
        //year
        $this->add(array(
            'name' => 'year',
            'type' => 'select',
            'attributes' => array(          
                'id'    => 'year',
            ),
            
            'options' => array(               
                'label' => 'Year*',
                'disable_inarray_validator' => true,
            ),
        ));
        
        //quarter
        $this->add(array(
            'name' => 'quater',
            'type' => 'select',
            'attributes' => array(          
                'id'    => 'quater',
            ),
            
            'options' => array(               
                'label' => 'Quater*',
                'disable_inarray_validator' => true,
            ),
        ));
        
        //month
        $this->add(array(
            'name' => 'month',
            'type' => 'select',
            'attributes' => array(          
                'id'    => 'month',
            ),
            
            'options' => array(               
                'label' => 'Month*',
                'disable_inarray_validator' => true,
            ),
        ));
        
        //month
        $this->add(array(
            'name' => 'week',
            'type' => 'select',
            'attributes' => array(          
                'id'    => 'week',
            ),
            
            'options' => array(               
                'label' => 'Week*',
                'disable_inarray_validator' => true,
            ),
        ));

        // from date
        $this->add(array(
            'name' => 'from',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'from',
            ),
            'options' => array(
                'label' => 'From date',
            ),
        ));

        // to date
        $this->add(array(
            'name' => 'to',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'to',
            ),
            'options' => array(
                'label' => 'To date',
            ),
        ));        
        

        //create report button
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Create report',
                'id' => 'submitbutton',
                'class' => 'btn btn-success'
            ),
        ));
        
        //create report button
        $this->add(array(
            'name' => 'submitcsv',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Create csv-report',
                'id' => 'submitcsv',
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

        return $filter;
    }

}
