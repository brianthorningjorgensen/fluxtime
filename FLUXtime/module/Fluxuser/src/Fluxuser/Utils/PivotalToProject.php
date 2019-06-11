<?php

namespace Fluxuser\Utils;

use DateTime;
use Doctrine\ORM\EntityManager;
use Fluxuser\Entity\Project;
use Fluxuser\Entity\Projectlabel;
use Fluxuser\Entity\Projectuser;
use Fluxuser\Entity\Task;
use Fluxuser\Entity\Taskowner;
use stdClass;

/**
 * Class to convert the importet projects, stories, labels and users from 
 * Pivotal tracker to FLUXtime.
 *
 * @author Anders Bo Rasmussen
 */
class PivotalToProject {

    private $projectMapper;
    private $labelMapper;
    private $userMapper;
    private $entityManager;
    private $pivotal;
    private $usersById;
    private $currentUser;

    /**
     * Initialize all the class ressources and import everything from 
     * Pivotal Tracker and convert it to FLUXtime.
     * 
     * @param EntityManager $entityManager
     */
    public function init(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
        $this->projectMapper = $entityManager->getRepository('Fluxuser\Entity\Project');
        $this->labelMapper   = $entityManager->getRepository('Fluxuser\Entity\Projectlabel');
        $this->userMapper    = $entityManager->getRepository('Fluxuser\Entity\Fluxuser');

        $this->pivotal = new PivotalTracker();
        $users = $this->userMapper->findAll();
        
        foreach ($users as $user) {
            $this->currentUser = $user;
            // Skip the user if no API token is found.
            if($user->getPivotaltrackerapi() != null && $user->getPivotaltrackerapi() != '') {
                $this->pivotal->setToken($user->getPivotaltrackerapi());
                $pivotalProjects = $this->pivotal->getProjects();
                foreach ($pivotalProjects as $pivotalProject) {
                    $this->createProject($pivotalProject);
                }
            }
        }
    }

    /**
     * This helper method import projects from Pivotal Tracker and converts 
     * them into FLUXtime projects, then it flush the projects to the database.
     * After that it imports the labels from the Pivotal Tracker project and 
     * converts them into FLUXtime projectLabels and flush the labels to the database.
     * After that it imports the stories from the Pivotal Tracker project and 
     * converts them into FLUXtime tasks and then it flush the tasks to the database.
     * 
     * Projects:
     * <ol>
     *   <li>Import projects from Pivotal Tracker</li>
     *   <li>Create FLUXtime projects from the importet projects</li>
     *   <li>Flush the newly created projects to the database</li>
     * </ol>
     * ProjectLabels:
     * <ol>
     *   <li>Import labels from Pivotal Tracker</li>
     *   <li>Create FLUXtime projectLabels from the importet labels</li>
     *   <li>Flush the newly created projectLabels to the database</li>
     * </ol>
     * Tasks:
     * <ol>
     *   <li>Import stories from Pivotal Tracker</li>
     *   <li>Create FLUXtime tasks from the importet stories</li>
     *   <li>Flush the newly created tasks to the database</li>
     * </ol>
     * 
     * @param stdClass $pivotalProject
     */
    private function createProject($pivotalProject) {
        $project = $this->projectMapper->findOneBy(array('secondid' => $pivotalProject->id));
        if (!isset($project)) {
            $project = new Project();
        }
        /* v REQUIRED BY DATABSE v */
        $project->setActive(1);
        $project->setState(1);
        $project->setCreatedate(new DateTime($pivotalProject->created_at));
        $project->setProjectname($pivotalProject->name);
        $project->setFkaccountid($this->currentUser->getFkaccountid());
        /* ^ REQUIRED BY DATABASE ^ */

        $project->setSecondid($pivotalProject->id);

        $this->entityManager->persist($project);
        $this->entityManager->flush();

        $this->loadUsersFromPivotalTracker($project);
        
        $this->bindUsersToProject($project);
        $this->entityManager->flush();
        
        $this->createLabels($project);
        $this->entityManager->flush();

        $this->createTasks($project);
        $this->entityManager->flush();
    }

    /**
     * This helper method belongs to @see createProject and is responsible for 
     * importing the labels from Pivotal Tracker, then converts them to FLUXtime 
     * projectLabels and flush the projectLabels to the database.
     * 
     * @param Project $project
     */
    private function createLabels($project) {
        $pivotalLabels = $this->pivotal->getProjectLabels($project->getSecondid());
        foreach ($pivotalLabels as $pivotalLabel) {
            $label = $this->labelMapper->findOneBy(array('secondid' => $pivotalLabel->id));
            if (!isset($label)) {
                $label = new Projectlabel();
            }

            /* v REQUIRED BY DATABSE v */
            $label->setLabelname($pivotalLabel->name);
            $label->setState(1);
            $label->setFkProjectid($project);
            $label->setFkaccountid($this->currentUser->getFkaccountid());
            /* ^ REQUIRED BY DATABASE ^ */

            $label->setSecondid($pivotalLabel->id);

            $this->entityManager->persist($label);
        }
    }

    /**
     * This helper method belongs to @see createProject and is responsible for 
     * importing the stories from Pivotal Tracker, then converts them to FLUXtime 
     * tasks and flush the tasks to the database.
     *  
     * @param Project $project
     */
    private function createTasks($project) {
        $pivotalTasks = $this->pivotal->getProjectStories($project->getSecondid());
        $taskMapper = $this->entityManager->getRepository('Fluxuser\Entity\Task');
        foreach ($pivotalTasks as $pivotalTask) {
            $task = $taskMapper->findOneBy(array('secondid' => $pivotalTask->id));
            if (!isset($task)) {
                $task = new Task();
            }
            
            /* v REQUIRED BY DATABSE v */
            $task->setTaskname($pivotalTask->name);
            $task->setFkprojectid($project);
            $task->setState(1);
            $task->setFkcreator(isset($this->usersById[$pivotalTask->requested_by_id]) ? $this->usersById[$pivotalTask->requested_by_id] : $this->userMapper->findOneBy(array('id' => 3)));
            $task->setStatus($pivotalTask->current_state);
            $task->setFkaccountid($this->currentUser->getFkaccountid());
            /* ^ REQUIRED BY DATABASE ^ */

            $task->setDescription(isset($pivotalTask->description) ? $pivotalTask->description : '' );
            $task->setFklabelid(isset($pivotalTask->labels[0]) ? $this->labelMapper->findOneBy(array('secondid' => $pivotalTask->labels[0]->id)) : null);
            $task->setPoints(isset($pivotalTask->estimate) ? $pivotalTask->estimate : null );
            $task->setSecondid($pivotalTask->id);
            $task->setTasktype($pivotalTask->story_type);
            
            $this->entityManager->persist($task);
            $this->bindUsersToTask($task);
        }
    }

    /**
     * This helper method belongs to @see createTask and is responsible for 
     * binding the user to the specified task.
     * 
     * @param Task $task
     */
    private function bindUsersToTask($task) {
        $taskOwnerMapper = $this->entityManager->getRepository('Fluxuser\Entity\Taskowner');
        $taskOwner = $taskOwnerMapper->findOneBy(array('fkuserid' => $this->currentUser->getId(), 'fktaskid' => $task->getTaskid()));
        if (!isset($taskOwner)) {
            $taskOwner = new Taskowner();
        }
        $taskOwner->setFktaskid($task);
        $taskOwner->setFkuserid($this->currentUser);
        $taskOwner->setFkaccountid($this->currentUser->getFkaccountid());

        $this->entityManager->persist($taskOwner);
    }

    /**
     * This helper method belongs to @see createProject and is responsible for 
     * binding the user to the specified project.
     * 
     * @param Project $project
     */
    private function bindUsersToProject($project) {
        $this->removeAllUsersFromProject($project);        
        foreach ($this->usersById as $user) {
            if (isset($user)) {
                $projectUserMapper = $this->entityManager->getRepository('Fluxuser\Entity\Projectuser');
                $projectUser = $projectUserMapper->findOneBy(array('fkUserid' => $user->getId(), 'fkProjectid' => $project->getProjectid()));
                if (!isset($projectUser)) {
                    $projectUser = new Projectuser();
                }
                $projectUser->setFkProjectid($project);
                $projectUser->setFkUserid($user);
                $projectUser->setFkaccountid($user->getFkaccountid());

                $this->entityManager->persist($projectUser);
            }
        }
    }
    
    private function removeAllUsersFromProject($project) {
        $projectUserMapper = $this->entityManager->getRepository('Fluxuser\Entity\Projectuser');
        $projectUsers = $projectUserMapper->findBy(array('fkProjectid' => $project->getProjectid()));
        foreach ($projectUsers as $projectUser) {
            $this->entityManager->remove($projectUser);
        }
        $this->entityManager->flush();
    }
    
    /**
     * Loads all users from a project into an array.
     * The array is being used when pivotal tracker is only providing an user-id,
     * So we can lookup the user.
     * 
     * @param Project $project
     */
    private function loadUsersFromPivotalTracker($project){
        $pivotalUsers = $this->pivotal->getProjectMemberships($project->getSecondid());
        $this->usersById = [];
        foreach ($pivotalUsers as $pivotalUser) {
            $this->usersById[$pivotalUser->person->id] = $this->userMapper->findOneBy(array('workEmail' => $pivotalUser->person->email));
        }
        
    }
    
}
