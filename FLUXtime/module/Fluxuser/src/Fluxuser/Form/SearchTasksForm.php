<?php

namespace Fluxuser\Form;

use Zend\Form\Form;

//Form til søgning af tasks i projekter 

class SearchTasksForm extends Form {

    public function __construct() {

        // we want to ignore the name passed
        parent::__construct('project');

        $this->setAttribute('method', 'post');

        
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
                'data-content' => "Search for task, label, creator, points, type or status",
            ),
        ));
      
    }

}
