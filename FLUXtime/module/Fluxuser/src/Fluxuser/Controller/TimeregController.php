<?php

namespace Fluxuser\Controller;

use Doctrine\ORM\EntityManager;
use Fluxuser\Utils\ActionHelper;
use DateTime;
use Fluxuser\Entity\Timereg;
use Fluxuser\Form\SearchTasksForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Fluxuser\Utils\Decryption;
use Fluxuser\Form\SearchTimeregForm;

class TimeregController extends ActionHelper {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct() {
        
    }

    /**
     * Henter aktive projekter som user hvor user er member eller projectmangt
     * @return JsonModel
     */
    public function ajaxfetchuserprojectsAction() {
        //Henter sidens request 
        $request = $this->getRequest();
        $post = $request->getPost();
        $id = $post['id'];
        if (!$id) {
            return $this->redirect()->toRoute('timereg', array('action' => 'add'));
        }
        $user = $this->getUsermapper()->find($id);
        $projectusers = $this->getProjectusermapper()->findBy(array('fkUserid' => $user));
         $projectarray = array();
         if(count($projectusers) > 0){
        foreach ($projectusers as $pu) {
            $project = $pu->getFkprojectid();
            if($project->getActive() == 1 && $project->getState() == 1){
            $projectarray[$project->getProjectid()] = $project->getProjectname();
            }
        }
         }
         if($user->getFkuserrole()->getId() != 2){
        $projects = $this->getProjectmapper()->findBy(array('fkProjectmanager' => $user, 'state' => 1, 'active' => 1));
        if(count($projects) > 0){
        foreach ($projects as $p) {
            $projectarray[$p->getProjectid()] = $p->getProjectname();
        }
         }
         }
        $result['id'] = $id;
        $result['projects'] = $projectarray;
          return new JsonModel($result);
    }

    /**
     * Henter labels for et projekt
     * @return JsonModel
     */
    public function ajaxfetchprojectlabelsAction() {
        $request = $this->getRequest();
        $post = $request->getPost();
        $id = $post['id'];
        //Hvis project id er null
        if (!$id) {
            //Redirect til liste med alle projects
            return $this->redirect()->toRoute('timereg', array('action' => 'add'));
        }
        $labels = $this->getLabelmapper()->findBy(array('fkProjectid' => $id, 'state' => 1));
         $labelsarray = array();
         if(count($labels) > 0){
        foreach ($labels as $label) {
            $labelsarray[$label->getLabelid()] = $label->getLabelname();
         }}
      
        $result['id'] = $id;
        $result['labels'] = $labelsarray;
        return new JsonModel($result);
    }

    /**
     * Henter label tasks
     * @return JsonModel
     */
    public function ajaxfetchlabeltaskAction() { 
        $request = $this->getRequest();
        $post = $request->getPost();
        $id = $post['id'];
        $pid = $post['pid'];
        if (!$id) {
            return $this->redirect()->toRoute('timereg', array('action' => 'add'));
        }
        if($id != 0){
            $tasks = $this->getTaskmapper()->findBy(array('fklabelid' => $id, 'fkprojectid' => $pid, 'state' => 1));
        } 
        else{
             $tasks = $this->getTaskmapper()->findBy(array('fklabelid' => null, 'fkprojectid' => $pid, 'state' => 1));
        }
        $taskarray = array();
        if(count($tasks) > 0){
        foreach ($tasks as $task) {
            $taskarray[$task->getTaskid()] = $task->getTaskname();
        }}
        $result['id'] = $id;
        $result['tasks'] = $taskarray;
        return new JsonModel($result);
    }

    /**
     * my log - brugerens egne timeregs 
     * @return type
     */
    public function mytimeregAction() {
        $request = $this->getRequest();
        $form = new \Fluxuser\Form\SearchTimeregForm($this->getEntityManager());
        $form->remove('active');
        // user
        $user = $this->identity();
        $userid = $user->getId();

        if ($userid) {
            $search = "";
            $searchfrom = "";
            $searchto = "";
            //Hvis click på button på form
            if ($request->isPost()) {
                $data = $request->getPost();
                $search = $data['search'];

                if ($data['searchfrom']) {
                    $searchfrom = str_replace(".", "-", $data['searchfrom'] . ':00');
                    $searchfrom = " and tr.timestart >= '" . $searchfrom . "' ";
                    if ($data['searchto']) {
                        $searchto = str_replace(".", "-", $data['searchto'] . ':00');
                        $searchto = " and tr.timestop <= '" . $searchto . "' ";
                    }
                }
                $form->setData($data);
            }
            $query = $this->getEntityManager()->createQuery(''
                    . 'SELECT tr, to, t, p, l '
                    . 'FROM Fluxuser\Entity\Timereg tr '
                    . 'JOIN tr.fktaskownerid to '
                    . 'JOIN to.fktaskid t '
                    . 'JOIN t.fkprojectid p '
                    
                    . 'JOIN to.fkuserid u '
                    . 'LEFT JOIN t.fklabelid l '
                    . "where "
                    . "t.state = 1 "
                    . "and u.id = '" . $this->identity()->getId() . "' "
                    . $searchfrom
                    . $searchto
                    . " and (t.taskname like '%" . $search . "%' "
                    . "or p.projectname like '%" . $search . "%' "
                    . "or l.labelname like '%" . $search . "%' ) "
                    . "order by t.taskname desc, tr.timestart asc"
            );

            $timeregs = $query->getResult();

            if ($timeregs) {
                return array('timeregs' => $timeregs, 'form' => $form);
            }
        }
        //Return nothing
        return array('form' => $form);
    }

    /**
     *  log / time registrations 
     * admin
     * @return type
     */
    public function indexAction() {
        $request = $this->getRequest();
        $form = new \Fluxuser\Form\SearchTimeregForm($this->getEntityManager());
        $form->remove('active');
        $user = $this->identity();
        $userid = $user->getId();
        if ($userid) {
            $search = "";
            $searchfrom = "";
            $searchto = "";
            //Hvis click på button på form
            if ($request->isPost()) {
                $data = $request->getPost();
                $search = $data['search'];

                if ($data['searchfrom']) {
                    $searchfrom = str_replace(".", "-", $data['searchfrom'] . ':00');
                    $searchfrom = " and tr.timestart >= '" . $searchfrom . "' ";
                    if ($data['searchto']) {
                        $searchto = str_replace(".", "-", $data['searchto'] . ':00');
                        $searchto = " and tr.timestop <= '" . $searchto . "' ";
                    }
                }
                $form->setData($data);
            }
            $query = $this->getEntityManager()->createQuery(''
                    . 'SELECT tr, to, t, p, l '
                    . 'FROM Fluxuser\Entity\Timereg tr '
                    . 'JOIN tr.fktaskownerid to '
                    . 'JOIN to.fktaskid t '
                    . 'JOIN t.fkprojectid p '
                   
                    . 'JOIN to.fkuserid u '
                     . 'LEFT JOIN t.fklabelid l '
                    . "where "
                    . "t.state = 1 "
                    . "and u.fkaccountid = '" . $this->identity()->getFkaccountid()->getAccountid() . "' "
                    . $searchfrom
                    . $searchto
                    . " and (t.taskname like '%" . $search . "%' "
                    . "or p.projectname like '%" . $search . "%' "
                    . "or l.labelname like '%" . $search . "%' ) "
                    . "order by t.taskname desc, tr.timestart asc"
            );
            $timeregs = $query->getResult();
            if ($timeregs) {
                return array('timeregs' => $timeregs, 'form' => $form);
            }
        }
        //Return nothing
        return array('form' => $form);
    }

    /**
     * Add time reg
     * @return type form
     */
    public function addAction() {
        $request = $this->getRequest();
        $form = $this->prepareAddForm();
        //Submit
        if ($request->isPost()) {
            //Validering
            $form->setData($request->getPost());
            $form->setInputFilter($form->getInputFilterSpecification());
            if ($form->isValid()) {
                $data = $form->getData();
                $timereg = $this->setFieldsCreateTimereg($data);
                //Cache data til hukommelse
                $this->getEntityManager()->persist($timereg);
                //Gemmer project i db 
                $this->getEntityManager()->flush();
                //Henter auto increment og redirect til edit (add task owners)
                return $this->redirect()->toRoute('timereg');
            } 
        }
        return array('form' => $form, 'messages' => $form->getMessages());
    }

    /**
     * Set fields on object - create timereg
     * @param type $data
     * @return Timereg
     */
    private function setFieldsCreateTimereg($data) {
        $timereg = new Timereg();
        $timereg->setFkaccountid($this->identity()->getFkaccountid());
        $timereg->setState(1);
        $timereg->setTimestart( new DateTime($data['from']) );
        $timereg->setTimestop( new DateTime($data['to']) );
        $taskowner = $this->getOwnermapper()->find($data['tasks']);
        $timereg->setFktaskownerid($taskowner);
        return $timereg;
    }

    /**
     * Prepare form add action
     * @param type $pid
     * @return ProjectTaskForm
     */
    private function prepareAddForm() {
        $form = new \Fluxuser\Form\TimeregForm($this->getEntityManager());
        $account = $this->identity()->getFkaccountid();
 $users = $this->getUsermapper()->findBy(array('state' => 1, 'fkaccountid' => $account), array('username' => 'DESC'));
$form->get('projects')->setEmptyOption('Please select...');
        $parray = [];
        if (count($users) > 0) {
            foreach ($users as $user) {
                $parray[$user->getId()] = $user->getUsername();
            }
        } else {
            $parray[0] = '';
        }
        $form->get('users')->setValueOptions($parray);
        return $form;
    }

    /**
     * Ajax delete timereg
     * @return JsonModel
     */
    public function ajaxdeleteAction() {
        //Delete timereg        
        //Henter sidens request 
        $request = $this->getRequest();
        $post = $request->getPost();
        $id = $post['id'];
        if (!$id) {
            return $this->redirect()->toRoute('timereg');
        }
        $timereg = $this->getTimeregmapper()->find($id);

        if ($timereg) {
            $this->getEntityManager()->remove($timereg);
            $this->getEntityManager()->flush();
        }
        $result['status'] = true;
        return new JsonModel($result);
    }

    /**
     * Ajax method for editing a time registration.
     * @return JsonModel
     */
    public function ajaxeditAction(){
        $request = $this->getRequest();
        
        $result['state'] = false;
        
        if ($request->isPost()) {
            $post = $request->getPost();
            $id = $post['id'];
            $from = $post['from'];
            $to = $post['to'];
            if (!$id || !$from || !$to) {
                return $this->redirect()->toRoute('timereg');
            }
            $timereg = $this->getTimeregmapper()->find($id);

            if ($timereg) {
                $timereg->setTimestart(new DateTime($from . ':00'));
                $timereg->setTimestop(new DateTime($to . ':00'));
                $this->getEntityManager()->persist($timereg);
                $this->getEntityManager()->flush();
                $result['state'] = true;
            } else {
                $result['error'] = 'Timereg not found!';
            }  
        }
        return new JsonModel($result);
    }

    /**
     * Prepare form add action
     * @param type $timereg
     * @return ProjectTaskForm
     */
    private function prepareEditForm($timereg) {
        $form = new \Fluxuser\Form\TimeregEditForm($this->getEntityManager());
        $form->get('from')->setValue($timereg->getTimestart()->format("Y-m-d H:i"));
        $form->get('to')->setValue($timereg->getTimestop()->format("Y-m-d H:i"));
        $form->get('submit')->setValue("Save time registration");
        return $form;
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
