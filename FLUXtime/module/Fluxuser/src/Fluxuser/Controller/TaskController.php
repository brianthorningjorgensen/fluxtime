<?php

namespace Fluxuser\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Fluxuser\Entity\Task;
use Fluxuser\Entity\Taskowner;
use Fluxuser\Form\OwnerForm;
use Fluxuser\Form\ProjectTaskForm;
use Fluxuser\Form\SearchTasksForm;
use Fluxuser\Form\SearchMyTasksForm;
use Fluxuser\Utils\ActionHelper;
use Fluxuser\Utils\PivotalTracker;
use Zend\View\Model\JsonModel;
use DateTime;

/**
 * Task controller
 */
class TaskController extends ActionHelper {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Index tasks - select project tasks & search
     * @return type project tasks, project og form
     */
    public function indexAction() {
        //Bruger som er logget på
        $identity = $this->identity();
        $request = $this->getRequest();
        $form = new \Fluxuser\Form\SearchTasksForm();
        //Hent project id fra url
        $pid = $this->decrypt($this->params()->fromRoute('id'), SECRET_KEY);
        //Hvis id er null
        if (!$pid) {
            //Redirect til liste med alle projects
            return $this->redirect()->toRoute('project');
        }
        //Project
        try{
        $project = $this->getProjectmapper()->find($pid);
         }catch(\Exception $e){
            return $this->redirect()->toRoute('project');
        }
        //Tasks
        $list = $this->getTaskmapper()->findBy(array('state' => 1, 'fkprojectid' => $project), array('taskid' => 'DESC'));
        $collection = new ArrayCollection($list);
        $accountid = $identity->getFkaccountid();
        $criteria = Criteria::create()->where(Criteria::expr()->eq('fkaccountid', $accountid));
        //Submit
        if ($request->isPost()) {
            $data = $request->getPost();
            $search = $data['search'];
            if ($search != null) {
                $criteria = $this->getTaskSearchCriteria($search, $accountid);
                $tasks = $collection->matching($criteria);
                return array('tasks' => $tasks, 'form' => $form, 'project' => $project);
            } else {
                $tasks = $collection->matching($criteria);
                return array('tasks' => $tasks, 'form' => $form, 'project' => $project);
            }
        }
        $tasks = $collection->matching($criteria);
        return array('tasks' => $tasks, 'form' => $form, 'project' => $project);
    }

    /**
     * Search task criterias
     * @param type $search
     * @return type criterias
     */
    private function getTaskSearchCriteria($search, $accountid) {
        $label = $this->getLabelmapper()->findOneBy(array('labelname' => $search, 'fkaccountid' => $accountid));
        $creator = $this->getUsermapper()->findOneBy(array('username' => $search, 'fkaccountid' => $accountid));
        $field = 'fklabelid';
        //Hvis label ikke er tilføjet til task, udføres en anden søgning som returnerer 0 tasks
        if ($label == null) {
            $field = 'state';
            $label = 100;
        }
        // Søgekriterier  
        $criteria = Criteria::create()->where(Criteria::expr()->eq('fkaccountid', $accountid))
                ->andWhere(Criteria::expr()->eq("status", $search))
                ->orWhere(Criteria::expr()->eq("points", $search))
                ->orWhere(Criteria::expr()->eq("tasktype", $search))
                ->orWhere(Criteria::expr()->eq("taskname", $search))
                ->orWhere(Criteria::expr()->eq("fkcreator", $creator))
                ->orWhere(Criteria::expr()->eq($field, $label));
        return $criteria;
    }

    /**
     * Add new task to project
     * @return type form og project
     */
    public function addAction() {
        $request = $this->getRequest();
        //Hent project id fra url
        $pid = $this->params()->fromRoute('id');
        //Submit
        if ($request->isPost()) {
            //Validering
            $project = $this->getProjectmapper()->find($pid);
            $form = new ProjectTaskForm($this->getEntityManager());
            $form->setData($request->getPost());
            $form->remove('fklabelid');
            $form->setInputFilter($form->getInputFilterSpecification());

            if ($form->isValid()) {
                $data = $form->getData();
                $task = $this->setFieldsCreateTask($project, $data);
                //Cache data til hukommelse
                $this->getEntityManager()->persist($task);
                //Gemmer project i db 
                $this->getEntityManager()->flush();
                //Henter auto increment og redirect til edit (add task owners)
                $taskid = $task->getTaskid();
                $encrypt = $this->encrypt($taskid, SECRET_KEY);
                $encryptid = str_replace('+', '%20', $encrypt);
                return $this->redirect()->toUrl('/task/edit/' . $encryptid);
            }
        }
        $pid = $this->decrypt($this->params()->fromRoute('id'), SECRET_KEY);
        //Hvis id er null
        if (!$pid) {
            //Redirect til liste med alle projects
            return $this->redirect()->toRoute('project');
        }
        try{
        $project = $this->getProjectmapper()->find($pid);
         }catch(\Exception $e){
            return $this->redirect()->toRoute('project');
        }
        $imported = false;
        if ($project->getSecondid() != null) {
            $imported = true;
        }
        $form = $this->prepareAddForm($project);
        return array('form' => $form, 'project' => $project, 'imported' => $imported, 'messages' => $form->getMessages());
    }

    /**
     * Prepare form add action
     * @param type $pid
     * @return ProjectTaskForm
     */
    private function prepareAddForm($project) {
        $form = new ProjectTaskForm($this->getEntityManager());
        //Bruges kun ved edit
        $form->remove('secondid');
        $form->remove('status');
        $form->remove('fkCreator');
        //Project labels i select box
        $labels = $this->getLabelmapper()->findBy(array('fkProjectid' => $project->getProjectid(), 'state' => 1));
        $form->get('fklabelid')->setEmptyOption('Please select...');
        $larray = [];
        if (count($labels) > 0) {
            foreach ($labels as $label) {
                $larray[$label->getLabelid()] = $label->getLabelname();
            }
            //ObjectSelect element må ikke være empty    
        } else {
            $larray[0] = '';
        }
        $form->get('fklabelid')->setValueOptions($larray);
        return $form;
    }

    /**
     * Set fields på task objekt - create task
     * @param type $project
     * @param type $data
     * @param type $labelMapper
     * @return Task
     */
    private function setFieldsCreateTask($project, $data) {
        //Bruger som er logget på 
        $identity = $this->identity();
        $task = new Task();
        $task->setDescription($data['description']);
        $task->setFkcreator($identity);
        $task->setStatus('unstarted');
        $task->setTaskname($data['taskname']);
        $task->setPoints($data['points']);
        $task->setTasktype($data['tasktype']);
        $labelid = $data['fklabelid'];
        if ($labelid != null) {
            $label = $this->getLabelmapper()->find($labelid);
            $task->setFklabelid($label);
        }
        $task->setState(1);
        $task->setFkprojectid($project);
        $task->setFkaccountid($identity->getFkaccountid());
        return $task;
    }

    /**
     * Edit at project task
     * redirected if project and task reference not valid
     * @return type
     */
    public function editAction() {
        $request = $this->getRequest();
        $taskid = $this->params()->fromRoute('id');
        //Submit  
        if ($request->isPost()) {
            $form = new ProjectTaskForm($this->getEntityManager());
            //Validering
            $form->setData($request->getPost());
            $task = $this->getTaskmapper()->find($taskid);
            $form->setInputFilter($form->getInputFilterSpecification());
            $data = $request->getPost();
            $labelid = $form->get('fklabelid')->getValue();
            $form->remove('fklabelid');
            if ($form->isValid()) {
                $taskSave = $this->setFieldsEditTask($task, $data, $labelid);
                //Cache data til hukommelse
                $this->getEntityManager()->persist($taskSave);
                //Gemmer project i db 
                $this->getEntityManager()->flush();
                $pid = $task->getFkprojectid()->getProjectid();
                $encrypt = $this->encrypt($pid, SECRET_KEY);
                $encryptid = str_replace('+', '%20', $encrypt);
                return $this->redirect()->toUrl('/task/index/' . $encryptid);
            }
        }
        $taskid = $this->decrypt($this->params()->fromRoute('id'), SECRET_KEY);
        //Hvis task id er null
        if (!$taskid) {
            //Redirect til liste med alle projects
            return $this->redirect()->toRoute('project');
        }
        // Task
        try{
        $task = $this->getTaskmapper()->find($taskid);
         }catch(\Exception $e){
            return $this->redirect()->toRoute('project');
        }
        $project = $task->getFkprojectid();
        $form = $this->prepareEditForm($task);

        //Bind object til form
        $form->setHydrator(new DoctrineHydrator($this->getEntityManager(), 'Fluxuser\Entity\Task'));
        $form->bind($task);
        $imported = false;
        if ($task->getSecondid() != null) {
            $form->remove('submit');
            $imported = true;
        }
        //Task owners
        $selectowners = $this->getOwnermapper()->findBy(array('fktaskid' => $task->getTaskid()));
        //Owner from til Add owner
        $formOwner = $this->prepareAddOwnerForm($selectowners, $task);
        //Returnere form
        return array(
            'form' => $form, 'task' => $task, 'owners' => $selectowners, 'formOwner' => $formOwner, 'imported' => $imported, 'messages' => $form->getMessages()
        );
    }

    /**
     * Prepare form to edit task
     * @param type $task
     * @return ProjectTaskForm
     */
    private function prepareEditForm($task) {
        $form = new ProjectTaskForm($this->getEntityManager());
        $form->get('fkCreator')->setValue($task->getFkcreator()->getUsername());
        $form->get('status')->setValue(array($task->getStatus() => $task->getStatus()));
        $project = $task->getFkprojectid();
        if ($task->getSecondid() == null) {
            $form->remove('secondid');
        }
        $projectid = $project->getProjectid();
        // project labels i selectbox
        $labels = $this->getLabelMapper()->findBy(array('fkProjectid' => $projectid, 'state' => 1));
        $form->get('fklabelid')->setEmptyOption('Please select...');
        if (count($labels) > 0) {
            $larray = [];
            foreach ($labels as $label) {
                $larray[$label->getLabelid()] = $label->getLabelname();
            }
            if ($task->getFklabelid() != null && $task->getFklabelid()->getState() != 0) {
                $form->get('fklabelid')->setValue(array($task->getFklabelid()->getLabelid() => $task->getFklabelid()->getLabelname()));
            }
        }
        //ObjectSelect element må ikke være empty  
        else {
            $larray[0] = '';
        }
        $form->get('fklabelid')->setValueOptions($larray);
        return $form;
    }

    /**
     * Prepare form to add owner
     * @param type $selectowners
     * @param type $task
     * @return OwnerForm
     */
    private function prepareAddOwnerForm($selectowners, $task) {
        $formOwner = new OwnerForm($this->getEntityManager());
        // Selectbox - henter ud fra projectusers
        $projectManager = $task->getFkprojectid()->getFkProjectmanager();
        $projectusers = $this->getProjectusermapper()->findBy(array('fkProjectid' => $task->getFkprojectid()));
        $oarray = [];
        if ($projectManager != null) {
            $oarray[$projectManager->getId()] = $projectManager->getUsername();
        }
        foreach ($projectusers as $projectuser) {
            $puser = $projectuser->getFkUserid();

            $addelement = true;
            foreach ($selectowners as $selectowner) {
                // if user allready in selectbox dont add
                if ($addelement && $puser->getId() == $selectowner->getFkUserid()->getId()) {
                    $addelement = false;
                }
            }
            if ($addelement) {
                $oarray[$puser->getId()] = $puser->getUsername();
            }
        }
        $formOwner->get('fkuserid')->setValueOptions($oarray);
        $formOwner->get('fktaskid')->setValue($task->getTaskid());
        return $formOwner;
    }

    /**
     * Set object fields
     * @param type $task
     * @param type $data
     * @param type $labelid
     * @return type task
     */
    private function setFieldsEditTask($task, $data, $labelid) {
        $task->setDescription($data['description']);
        $task->setStatus($data['status']);
        $task->setTaskname($data['taskname']);
        $task->setPoints($data['points']);
        $task->setTasktype($data['tasktype']);
        if ($labelid != null) {
            $label = $this->getLabelmapper()->find($labelid);
            $task->setFklabelid($label);
        } else {
            $task->setFklabelid(null);
        }
        return $task;
    }

    /**
     * AJAX delete a task
     * @return JsonModel
     */
    public function ajaxdeleteAction() {
        $post = $this->getRequest()->getPost();
        $id = $post['id'];
        $result['success'] = false;
        $task = $this->getTaskmapper()->find($id);
        $taskowners = $this->getOwnermapper()->findBy(array('fktaskid' => $id));
        $timeregs = $this->getTimeregmapper()->findBy(array('fktaskid' => $id, 'state' => 1));
        //Can only be deleted if no timeregs added
        if ($task != null && count($timeregs) == 0) {
            // remove task owners with task id
            foreach ($taskowners as $owner) {
                $this->getEntityManager()->remove($owner);
            }
            $task->setState(0);
            //Cache delete til hukommelse
            $this->getEntityManager()->persist($task);
            //fjerner fra db 
            $this->getEntityManager()->flush();
            $result['success'] = true;
        }
        return new JsonModel($result);
    }

    /**
     * AJAX add owner 
     * @return \Fluxuser\Controller\JsonModel
     */
    public function ajaxaddownerAction() {
        $post = $this->getRequest()->getPost();
        $id = $this->addowner($post);
        $bool = 'false';
        if ($id > 0) {
            $bool = 'true';
        }
        //$result['success'] = true;
        $result['id'] = $id . $bool;
        return new JsonModel($result);
    }

    /**
     * Add task owner
     * @param type $post
     * @return type int id
     */
    private function addowner($post) {
        //Form vil altid være valid
        $owner = $this->getUsermapper()->find($post['fkuserid']);
        $task = $this->getTaskmapper()->find($post['fktaskid']);
        $taskowner = new Taskowner();
        $taskowner->setFktaskid($task);
        $taskowner->setFkuserid($owner);
        $taskowner->setFkaccountid($this->identity()->getFkaccountid());
        //Cache data til hukommelse
        $this->getEntityManager()->persist($taskowner);
        //Gemmer label i db 
        $this->getEntityManager()->flush();
        $id = $taskowner->getTaskownerid();
        return $id;
    }

    /**
     * Ajax - remove task owner
     * @return JsonModel
     */
    public function ajaxremoveownerAction() {
        // Henter data og sætter form
        $post = $this->getRequest()->getPost();
        $id = $post['id'];
        $result['success'] = false;
        //remove owner
        $ownertask = $this->getOwnermapper()->find($id);
        if ($ownertask) {
            //Cache data til hukommelse
            $this->getEntityManager()->remove($ownertask);
            //Gemmer label i db 
            $this->getEntityManager()->flush();
            $result['success'] = true;
        }
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

    

}
