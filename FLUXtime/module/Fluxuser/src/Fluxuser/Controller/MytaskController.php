<?php

namespace Fluxuser\Controller;

use Doctrine\ORM\EntityManager;
use Fluxuser\Entity\Timereg;
use Fluxuser\Form\SearchMyTasksForm;
use Fluxuser\Utils\ActionHelper;
use Fluxuser\Utils\PivotalTracker;
use Zend\View\Model\JsonModel;
use DateTime;

/**
 * Task controller
 */
class MytaskController extends ActionHelper {

    /**
     * @var EntityManager
     */
    protected $entityManager;

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
     * Henter current user tasks 
     * @return type array[project]
     */
    public function mytasksAction() {
        $request = $this->getRequest();
        //Ny form laves 
        $form = new SearchMyTasksForm($this->getEntityManager());
        $form->remove('active');
        // user
        $user = $this->identity();
        $userid = $user->getId();

        if ($userid) {
            $search = "";
            //Hvis click på button på form
            if ($request->isPost()) {
                $data = $request->getPost();
                $search = $data['search'];
            }$query = $this->getEntityManager()->createQuery(''
                    . 'SELECT to, t, p '
                    . 'FROM Fluxuser\Entity\Taskowner to '
                    . 'JOIN to.fktaskid t '
                    . 'JOIN t.fkprojectid p '
                    . 'JOIN to.fkuserid u '
                    . 'LEFT JOIN t.fklabelid l '
                    . "where "
                    . "t.state = 1 "
                    . "and u.id = '" . $this->identity()->getId() . "' "
                    . "and (t.status = 'unstarted' or t.status = 'planned' or t.status = 'rejected' or t.status = 'unscheduled' or t.status = 'started') "
                    . "and (t.taskname like '%" . $search . "%' "
                    . "or p.projectname like '%" . $search . "%' "
                    . "or t.status like '%" . $search . "%' "
                    . "or l.labelname like '%" . $search . "%' ) "
                    . "order by t.status desc"
            );

            $taskowners = $query->getResult();
            if ($taskowners) {
                foreach ($taskowners as $taskowner) {
                    $owners[$taskowner->getFktaskid()->getTaskid()] = $taskowner->getTaskownerid();
                    $tasks[$taskowner->getFktaskid()->getTaskid()] = $taskowner->getFktaskid();
                }
                $tasktime = $this->calculatetime($tasks);
                $runningTask = $this->getRunningTask();
                if ($runningTask != null) {
                    $owner = $this->getOwnermapper()->findOneBy(array('fkuserid' => $this->identity(), 'fktaskid' => $runningTask));
                    $timereg = $this->getTimeregmapper()->findOneBy(array('fktaskownerid' => $owner, 'state' => 1, 'timestop' => null));

                    $form->get('newId')->setValue($timereg->getTimeregid());
                }
                return array('tasks' => $tasks, 'form' => $form, 'taskowners' => $owners, 'tasktime' => $tasktime, 'runningTask' => $runningTask);
            }
        }
        //Return nothing
        return array();
    }

    /**
     * Samlet tid på alle tidsregistreringer på en opgave
     * @param type $tasks
     * @return string
     */
    public function calculatetime($tasks) {
        if ($tasks != null) {
            $tasktime = array();
            // task timeconsumption
            foreach ($tasks as $task) {
                $elapsedtime = $this->taskConsumption($task);
                $tasktime[$task->getTaskid()] = '' . $this->formatElapsedTime($elapsedtime);
            }
            return $tasktime;
        }
    }

    /**
     * Beregn tidsforbrug for task
     * @return JsonModel
     */
    public function ajaxcalculatesingletasktimeAction() {
        //Henter sidens request 
        $request = $this->getRequest();
        $post = $request->getPost();
        //Hent id fra post
        $taskid = (int) $post['id'];
        $task = $this->getTaskmapper()->findOneBy(array('taskid' => $taskid));
        $result['success'] = true;
        $totaltime = $this->taskConsumption($task);
        $result['totaltasktime'] = $totaltime;
        return new \Zend\View\Model\JsonModel($result);
    }

    /**
     * Format a integer to a String with format HOURS:MINUTES:SECONDS
     * @param type $thetime
     * @return type
     */
    private function formatElapsedTime($thetime) {
        return $tasktimestring = floor($thetime / 3600) . ':' . floor(($thetime / 60) % 60) . ':' . $thetime % 60;
    }

    /**
     * Tidsforbrug på opgaven
     * @param type $task
     * @return type
     */
    private function taskConsumption($task) {
        $totaltime = null;
        if ($task) {
            $taskowner = $this->getOwnermapper()->findOneBy(array('fkuserid' => $this->identity()->getId(), 'fktaskid' => $task->getTaskid()));
            $allTimes = $this->getTimeregmapper()->findBy(array('fktaskownerid' => $taskowner->getTaskownerid()), array('timestart' => 'DESC'));
            $totaltime = 0;

            foreach ($allTimes as $timereg) {

                if ($timereg != null && $timereg->getTimestop() != null) {
                    $start = $timereg->getTimestart();
                    $stop = $timereg->getTimestop();
                } else {
                    $start = $timereg->getTimestart();
                    $stop = new DateTime();
                }
                $diff = $stop->getTimestamp() - $start->getTimestamp();
                $totaltime = $totaltime + $diff;
            }
        }
        return $totaltime;
    }

    /**
     * Ajax start 
     * @return JsonModel
     */
    public function ajaxstarttimeregAction() {
        $runningTask = $this->getRunningTask();
        if ($runningTask !== null) {
            $taskowner = $this->getOwnermapper()->findOneBy(array('fkuserid' => $this->identity(), 'fktaskid' => $runningTask));
            $tr = $this->getTimeregmapper()->findOneBy(array("fktaskownerid" => $taskowner, "timestop" => null));
            $tr->setTimestop(new DateTime());
            $this->getEntityManager()->persist($tr);
            $this->getEntityManager()->flush();
            echo 'helloe';
        }
        $request = $this->getRequest();
        $post = $request->getPost();
        $id = (int) $post['id'];
        $task = $this->getTaskmapper()->findOneBy(array('taskid' => $id));
        if (!$task) {
            return $this->redirect()->toRoute('mytask');
        }
        $taskowner = $this->getOwnermapper()->findOneBy(array('fkuserid' => $this->identity(), 'fktaskid' => $task));
        // create new timeregistretion start
        $timereg = new Timereg();
        $timereg->setFkaccountid($this->identity()->getFkaccountid());
        $timereg->setFktaskownerid($taskowner);
        $timereg->setState(1);
        $timereg->setTimestart(new DateTime());
        $this->getEntityManager()->persist($timereg);
        // change taskobject to status Started
        $task->setStatus("started");
        if ($task->getSecondid() != null) {
            $this->startTaskInPT($task->getTaskid());
        }
        $newid = $timereg->getTimeregid();
        $id = 'newid' . $newid;
        if ($newid == null) {
            $id = false;
        }
        $this->getEntityManager()->flush();
        $result['status'] = $id;
        return new JsonModel($result);
    }

    /**
     * AJAX stop timer time registration 
     * @return JsonModel
     */
    public function ajaxstoptimeregAction() {
        $request = $this->getRequest();
        $post = $request->getPost();
        $id = (int) $post['id'];
        $timereg = $this->getTimeregmapper()->find(array('timeregid' => $id));
        if (!$timereg) {
            //Redirect 
            return $this->redirect()->toRoute('mytask');
        }
        $timereg->setTimestop(new DateTime());
        $this->getEntityManager()->persist($timereg);
        $this->getEntityManager()->flush();
        $result['status'] = true;
        return new JsonModel($result);
    }

    /**
     * Finish task
     * @return JsonModel
     */
    public function ajaxfinishtaskAction() {
        $request = $this->getRequest();
        $post = $request->getPost();
        $result['success'] = $post['id'];
        //Hent id fra post
        $taskid = (int) $post['id'];
        if ($taskid != 0) {
            $task = $this->getTaskmapper()->findOneBy(array('taskid' => $taskid));
            $task->setStatus('finished');
            $this->getEntityManager()->persist($task);
            if ($task->getSecondid() != null) {
                $this->finishTaskInPT($taskid);
            }
            $owner = $this->getOwnermapper()->findOneBy(array('fkuserid' => $this->identity(), 'fktaskid' => $task));
            $timereg = $this->getTimeregmapper()->findOneBy(array('fktaskownerid' => $owner, 'state' => 1, 'timestop' => null));
            if ($timereg != null) {
                $dt = new DateTime();
                $timereg->setTimestop($dt);
                $this->getEntityManager()->persist($timereg);
            }
            $this->getEntityManager()->flush();

            $result['status'] = true;
        }
        return new \Zend\View\Model\JsonModel($result);
    }

    /**
     * Starts a task in PT
     * @return JsonModel
     */
    public function startTaskInPT($taskid) {

        if ($taskid != NULL) {
            $task = $this->getTaskmapper()->findOneBy(array('taskid' => $taskid));

            $projectid = $task->getFkprojectid()->getSecondid();
            $storyid = $task->getSecondid();

            if ($storyid != NULL) {
                $ok = $this->setTaskStateInPT($projectid, $storyid, PivotalTracker::STARTED);
            }
        }
    }

    /**
     * Finish a task in PT
     * @return JsonModel
     */
    public function finishTaskInPT($taskid) {
        if ($taskid != NULL) {
            $task = $this->getTaskmapper()->findOneBy(array('taskid' => $taskid));

            $projectid = $task->getFkprojectid()->getSecondid();
            $storyid = $task->getSecondid();
            if ($storyid != NULL) {
                $ok = $this->setTaskStateInPT($projectid, $storyid, PivotalTracker::FINISHED);
            }
        }
    }

    /**
     * Set the state of a task
     * @param type $projectid
     * @param type $storyid
     * @param type $state
     */
    public function setTaskStateInPT($projectid, $storyid, $state) {
        $pivotalTracker = new PivotalTracker();
        $pivotalTracker->setToken($this->identity()->getPivotaltrackerapi());
        $pivotalTracker->setProjectStoryState($projectid, $storyid, $state);
        return true;
    }

    /**
     * Gets running task
     * @return type
     */
    public function getRunningTask() {
        $identity = $this->identity();
        $taskowners = $this->getOwnermapper()->findBy(array('fkuserid' => $identity));
        $task = NULL;
        foreach ($taskowners as $taskowner) {
            $timereg = $this->getTimeregmapper()->findOneBy(array('fktaskownerid' => $taskowner, 'timestop' => null));
            if ($timereg != NULL) {
                $task = $timereg->getFktaskownerid()->getFktaskid();
            }
        }
        return $task;
    }

}
