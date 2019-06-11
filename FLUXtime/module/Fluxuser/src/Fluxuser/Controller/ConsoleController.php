<?php

namespace Fluxuser\Controller;

use Fluxuser\Utils\ActionHelper;
use Fluxuser\Utils\PivotalToProject;

/**
 * Description of ConsoleController
 *
 * @author Anders Bo Rasmussen
 */
class ConsoleController extends ActionHelper {
    
    public function cronTasksAction() {
        $this->syncWithPivotalTracker();
    }
    
     public function midnightmailAction() {
        $this->sendMidnightmail();
    }
    
    /**
     * Sync with PT
     */
    private function syncWithPivotalTracker() {
        echo 'starting syncWithPivotalTracker' . PHP_EOL;
        $pivotal = new PivotalToProject();
        $pivotal->init($this->getEntityManager());
        echo 'finished syncWithPivotalTracker' . PHP_EOL; 
    }
    
    /**
     * Stop running tasks every midnight and send email to user
     */
    private function sendMidnightmail(){
        $timeregs = $this->getRunningTasks();
        foreach($timeregs as $tr){
           $name = $tr->getFktaskownerid()->getFkuserid()->getFirstname();
           $setTo = $tr->getFktaskownerid()->getFkuserid()->getWorkEmail();
           $user = $tr->getFktaskownerid()->getFkuserid();
            $emailtype = $this->getEmailtypemapper()->find(4);
            $email = new Email();
            $email->setEmailtypefk($emailtype);
            $senttime = new DateTime(date("Y-m-d H:i:s"));
            $email->setSenttime($senttime);
            $email->setUserfk($user);
            $email->setFkaccountid($user->getFkaccountid());
            $tr->setTimestop($senttime);
            $this->getEntityManager()->persist($email);
            $this->getEntityManager()->persist($tr);
            // send reset password email
            $this->sendMidnightmessage($name, $setTo);
            //Gemmer email i databasen
            $this->getEntityManager()->flush();
        }
    }
    
      /**
     * Gets the active task
     * @return type timeregs
     */
    public function getRunningTasks() {
            $timeregs = $this->getTimeregmapper()->findBy(array('timestop' => null, 'state' => 1));
        return $timeregs;
    }
    
}
