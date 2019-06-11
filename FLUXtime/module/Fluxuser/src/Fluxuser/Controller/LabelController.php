<?php

namespace Fluxuser\Controller;

use Doctrine\ORM\EntityManager;
use Fluxuser\Entity\Projectlabel;
use Fluxuser\Form\LabelForm;
use Fluxuser\Utils\ActionHelper;
use Zend\View\Model\JsonModel;

class LabelController extends ActionHelper {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Ajax-kald, add label
     * @return JsonModel
     */
    public function ajaxaddlabelAction() {
        $form = new LabelForm($this->getEntityManager());
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $form->setData($this->getRequest()->getPost());
        }
        //Returnerer auto increment fra db
        $newId = $this->addLabel($form);
        if($newId != false){
        $result['result'] = 'newid' . $newId;
        } else {
         $result['result'] = false;    
        }
        return new JsonModel($result);
    }

    /**
     * Add label
     * @param type $form
     * @return type id
     */
    private function addLabel($form) {
        $account = $this->identity()->getFkaccountid();
        //Valirere form
        $form->setInputFilter($form->getInputFilterSpecification());
        $value = $form->get('labelname')->getValue();
        $pid = $form->get('fkProjectid')->getValue();
        $field = 'labelname';
        $exists = $this->recordExistCreate($field, $value, $pid);  
        if ($form->isValid() && !$exists){ 
            //Laver ny label
            $label = new Projectlabel();
            //Henter project-object fra db
            $project = $this->getProjectmapper()->find($form->get('fkProjectid')->getValue());
            //Sætter fields
            $label->setFkProjectid($project);
            $label->setLabelname($form->get('labelname')->getValue());
            $label->setFkaccountid($account);
            //Aktiv
            $label->setState(1);
            //Cache data til hukommelse
            $this->getEntityManager()->persist($label);
            //Gemmer label i db 
            $this->getEntityManager()->flush();
            //Returnerer auto increment
            return $label->getLabelid();
        }
        else if($exists){
            return false;
        }
    }

    /**
     * Ajax call edit label
     * @return JsonModel
     */
    public function ajaxeditlabelAction() {
        $form = new LabelForm($this->getEntityManager());
        // Henter data og sætter form
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $form->setData($this->getRequest()->getPost());
        }
        //Returnerer result
        $status = $this->editLabel($form);
        $result['status'] = $status;
        return new JsonModel($result);
    }

    /**
     * edit label
     * @param type $form
     * @return string
     */
    private function editLabel($form) {
        //Validere form
        $form->setInputFilter($form->getInputFilterSpecification());
        $value = $form->get('labelname')->getValue();
        $pid = $form->get('fkProjectid')->getValue();
        $field = 'labelname';
        $id = $form->get('labelid')->getValue();
         $exists = $this->recordExistEdit($field, $value, $pid, $id); 
        if ($form->isValid() && !$exists) {
            //Henter object fra db
            $label = $this->getLabelmapper()->find($form->get('labelid')->getValue());
            //Sætter label
            $label->setLabelname($form->get('labelname')->getValue());
            //Cache data til hukommelse
            $this->getEntityManager()->persist($label);
            //Gemmer label i db 
            $this->getEntityManager()->flush();
            //Returnerer auto increment
            return true;
        }
        else if($exists){
            return false;
        }
    }

    /**
     * Delete label
     * @param type $id
     * @return boolean
     */
    private function deletelabel($id) {
        $tasks = $this->getTaskmapper()->findBy(array('fklabelid' => $id));
        if ($id != null && count($tasks) == 0) {
            $label = $this->getLabelmapper()->find($id);

            //Sæt state til 0
            $label->setState(0);
            //Cache til hukommelse
            $this->getEntityManager()->persist($label);
            //Gemmer user i db
            $this->getEntityManager()->flush();
            //Return result
            return true;
        } else {
            return false;
        }
    }

    /**
     * Ajax-kald - delete label
     * @return JsonModel
     */
    public function ajaxconfirmdeletelabelAction() {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $data = $request->getPost();
        }
        $id = $data['id'];
        $ok = $this->deletelabel($id);
        $result['status'] = $ok;
        return new JsonModel($result);
    }

    /**
     * Sets the EntityManager
     * @param EntityManager $em
     * @access protected
     * @return PostController
     */
    protected function setEntityManager(EntityManager $em) {
        $this->entityManager = $em;
        return $this;
    }

    /**
     * Returns the EntityManager
     * Fetches the EntityManager from ServiceLocator if it has not been initiated
     * and then returns it
     * @access protected
     * @return EntityManager
     */
    protected function getEntityManager() {
        if (null === $this->entityManager) {
            $this->setEntityManager($this->getServiceLocator()->get('doctrine.entitymanager.orm_default'));
        }
        return $this->entityManager;
    }

   

    /**
     * Check if value exists in Database - create label
     * @param type $projectid
     * @param type $field
     * @param type $value
     */
    private function recordExistCreate($field, $value, $pid) {
        $project = $this->getProjectmapper()->find($pid);
        //Hent udvalgt i db
        $label = $this->getLabelmapper()->findOneBy(array($field => $value, 'fkProjectid' => $project));
        $exists = true;
        if ($label === NULL) {
          $exists = false;
        } 
        return $exists;
    }
    
    /**
     * Check if value exists in Database - edit label
     * @param type $field
     * @param type $value
     * @param type $pid
     * @param type $id
     * @return boolean
     */
    private function recordExistEdit($field, $value, $pid, $id) {
        $project = $this->getProjectmapper()->find($pid);
        //Hent udvalgt i db
        $label = $this->getLabelmapper()->findOneBy(array($field => $value, 'fkProjectid' => $project));
        if ($label === NULL) {
          return false;
        } 
        else {
          if($label->getLabelid() === $id){
           return false;   
          }  if($label->getLabelid() != $id){
           return true;   
          }
        } 
    }

}
