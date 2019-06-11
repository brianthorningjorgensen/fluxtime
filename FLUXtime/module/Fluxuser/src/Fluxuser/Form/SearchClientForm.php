<?php

namespace Fluxuser\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;

class SearchClientForm extends Form {

    private $EntityManager;

    public function __construct(EntityManager $entityManager) {

        $this->EntityManager = $entityManager;

        // we want to ignore the name passed
        parent::__construct('searchclient');

        $this->setAttribute('method', 'post');

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
                'data-content' => "Search for client name or client ID",
            ),
        ));
    }

}
