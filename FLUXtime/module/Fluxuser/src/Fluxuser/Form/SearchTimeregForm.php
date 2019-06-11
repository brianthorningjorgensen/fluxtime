<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use DateTime;

//Form til søgning af projekter (text field og søg-button)

class SearchTimeregForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {

        $this->EntityManager = $entityManager;

        // we want to ignore the name passed
        parent::__construct('project');

        $this->setAttribute('method', 'post');
       //  $this->setAttribute('class', 'form-inline');
        
        $this->add(array(
            'name' => 'active',
            'type' => 'checkbox',
            'options' => array(
                   'checked_value' => 1,
                    'unchecked_value' => 0,
                    'label' => 'ACTIVE',
                ),
                 'attributes' => array(
                'class' => 'checkbox',
                 'id' => 'searchcheckbox-popover',
                'data-toggle' => "popover",
                'data-placement' => "bottom",
                'title' => "Show active projects",
                'data-content' => "Show only active projects - uncheck and press search button to show inactive",
          ),
        ));
        
        //Button med lup-icon
        $this->add(array(
            'type' => 'Button',
            'name' => 'search',
            'options' => array(
                'label' => '<i class="fa fa-search"></i>',
                'label_options' => array(
                    'disable_html_escape' => true,
                )
            ),
            'attributes' => array(
                'type' => 'submit',
                'class' => 'button'
            )
        ));
        
        //Text field - søgeord
        $this->add(array(
            'name' => 'search',
            'label' => '',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'textfield',
                'id' => 'search-popover',
                'data-toggle' => "popover",
                'data-placement' => "bottom",
                'title' => "Search bar",
                'data-content' => "You can search for task name, label or project",
            ),
        ));
       
         $currentdate = new DateTime();
        $yesterdaydate = clone $currentdate;
        $yesterdaydate->modify('-1 day');
        //Text field - søgeord
        $this->add(array(
            'name' => 'searchto',
            'label' => '',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'textfield',
                'value' => $currentdate->format("Y-m-d H:i"),
                'id' => 'search-to',
                'title' => "To date",
                'data-content' => "To date and time through calendar. ",
            ),
        ));        
        
       
        
        //Text field - søgeord
        $this->add(array(
            'name' => 'searchfrom',
            'label' => '',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'textfield',
                'id' => 'search-from',
                'value' => $yesterdaydate->format("Y-m-d H:i") ,
                'title' => "From date",
                'data-content' => "From date and time through calendar. ",
            ),
        ));

      
    }

}
