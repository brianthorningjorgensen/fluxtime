<?php

namespace Fluxuser\Controller;

use Doctrine\ORM\EntityManager;
use Fluxuser\Form\ReportForm;
use Fluxuser\Utils\ActionHelper;
use DateTime;
use Zend\View\Model\ViewModel;

class ReportController extends ActionHelper  {

/**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct() {
    }
    
    /**
     *  an overview of all clients, projects and users to be used in reports
     * admin
     * @return type
     */
    public function indexAction() {
        //Hvis project manager
        if($this->identity()->getFkuserrole()->getId() == 4){
            return $this->redirect()->toRoute('report', array('action' => 'project'));
        }
        // På rapport siden 
        $account = $this->identity()->getFkaccountid();
        $clients = $this->getClientmapper()->findBy(array('fkaccountid' => $account, 'state' => 1), array('clientid' => 'DESC'));
        $clientnames = array();
        
        foreach($clients as $client) {
            $projects = $this->getProjectmapper()->findBy(array('fkaccountid' => $account, 'fkclientid' => $client->getClientid(), 'state' => 1), array('projectid' => 'ASC'));
            $projectnames  = array();
            foreach($projects as $project) {  
                 $projectmanager = $project->getFkProjectmanager();
                $projectusernames  = array();
                if($projectmanager != null){
                      $projectusernames[$projectmanager->getId()] = $projectmanager->getUsername();
                }
                $projectusers = $this->getProjectusermapper()->findBy(array('fkaccountid' => $account, 'fkProjectid' => $project->getProjectid()), array('projectuserid' => 'ASC'));
                foreach($projectusers as $projectuser) {
                    $projectusernames[$projectuser->getFkuserid()->getUsername()] = $projectuser->getFkuserid()->getUsername();
                }
                $projectnames[$project->getProjectname()] = $projectusernames;
               
            }
            $clientnames[$client->getClientname()] = $projectnames;
        }
        // values to the view
        return array('clients' => $clientnames);
        
    }
    
    /**
     * clients reports
     * admin
     * @return type
     */
    public function clientAction() {
        //På siden report 
        //Henter sidens request 
        $request = $this->getRequest();
        
        $account = $this->identity()->getFkaccountid();
        $clients = $this->getClientmapper()->findBy(array('fkaccountid' => $account, 'state' => 1), array('clientid' => 'DESC'));
        $timeregs = null;
        
        $form = new ReportForm($this->getEntityManager());
        $form->get('choices')->setValueOptions( array("Please select", "Year","Month","week", "Quater","Period"));
        $data = $request->getPost();
        $clientsparam = $data['clientcheckbox']; 
       
        $year = $data['year'];   

        $clientchecked = array();
        if ($request->isPost() && isset($clientsparam) ) {
            $clientwhere = "";
            $counter = 0;    
            $year = (new DateTime())->format('Y');
            foreach ( $clientsparam as $client ) {
                $method = 'or';
                if ($counter==0) {
                    $counter++;
                    $method = 'and (';                    
                }                
                $clientwhere .= $method . " c.clientname = '" . $client . "' ";
                $clientchecked[$client] = true; 
            }
            
            $clientwhere .= ')';
            if (strlen($clientwhere)<2) {
                $clientwhere = "";
            }
            
            $timelimiting = $this->getTimeQuery($form, $data);
            
            // søg resultater
            $textquery =    'SELECT tr, to, t, p, c '
                            . 'FROM Fluxuser\Entity\Timereg tr '
                            . 'JOIN tr.fktaskownerid to '
                            . 'JOIN to.fktaskid t '
                            . 'JOIN t.fkprojectid p '
                            . 'JOIN p.fkclientid c '
                            . 'JOIN t.fklabelid l '
                            . 'JOIN to.fkuserid u '
                            . "where "
                            . "t.state = 1 "           
                            . $timelimiting
                            . $clientwhere                            
                            . " order by t.status desc";
            $query = $this->getEntityManager()->createQuery( $textquery);
          //  echo '<br><br><br>' . $textquery;
           
            $timeregs = $query->getResult();
           
            // save content to file
            if (isset($data['submitcvs'])) {               
                // create unique filename of time username and userreport
                $currentdatetime = new DateTime();
                $userid = $this->identity()->getUsername();
                $filename = 'Projectreport_' . $userid . '-' . $currentdatetime->format("Y-m-d-H-i-s") . '.csv';
               
                // create array of rows for the csv
                $myarray = array();
                foreach( $timeregs as $timereg ) {
                    // beregn total diff i timeregistration
                    $from = $timereg->getTimestart();
                    $to = $timereg->getTimestop();
                    $diff = $to->getTimestamp() - $from->getTimestamp();
                    $difftime = floor($diff / 3600) . ':' . floor(($diff / 60) % 60) . ':' . $diff % 60;
                    
                    // define row in csvfile
                    $row[] =    array(      'Taskid' => $timereg->getFktaskownerid()->getFktaskid()->getTaskid(),
                                            'Taskname' => $timereg->getFktaskownerid()->getFktaskid()->getTaskname(),
                                            'Client' => $timereg->getFktaskownerid()->getFktaskid()->getFkprojectid()->getFkclientid()->getClientname(),
                                            'Project' => $timereg->getFktaskownerid()->getFktaskid()->getFkprojectid()->getProjectname(),
                                            'Label' => $timereg->getFktaskownerid()->getFktaskid()->getFklabelid()->getLabelname(),
                                            'Username' => $timereg->getFktaskownerid()->getFkuserid()->getUsername(),
                                            'Imported' => $timereg->getFktaskownerid()->getFktaskid()->getFkprojectid()->getSecondid(),
                                            'Fra' => $timereg->getTimestart()->format("Y-m-d H-i-s"),
                                            'Til' => $timereg->getTimestop()->format("Y-m-d H-i-s"),
                                            'Samlet' => $difftime
                                );   
                }

                return $this->csvAction($filename, $row);
            }

        }

        // weeks list of current year
        $totalweeksinyear = $this->getWeeksInYear($year);
        $weeks = $this->getWeeks($totalweeksinyear);
        $form->get('week')->setValueOptions( $weeks );
        
        // years 
        $years = $this->getYears();
        $form->get('year')->setValueOptions( $years ); 
        
        // kvartal
        $form->get('quater')->setValueOptions( $this->getQuaternames() );   
        
        // month
        $month_names = $this->getMonthnames();
        $form->get('month')->setValueOptions($month_names );   

        // values to the view
        return array('form' => $form, "clients" => $clients, "timeregs" => $timeregs, 'clientchecked' => $clientchecked );
    }
    
    /**
     *  project reports
     * admin
     * @return type
     */
    public function projectAction() {
        //På siden report 
        
        //Henter sidens request 
        $request = $this->getRequest();
        
        $account = $this->identity()->getFkaccountid();
        if($this->identity()->getFkuserrole()->getId() == 4){
            $pmid = $this->identity()->getid();
            $projects = $this->getProjectmapper()->findBy(array('fkaccountid' => $account, 'state' => 1, 'fkProjectmanager' => $pmid), array('projectid' => 'DESC'));                        
        } else {
            // admin
            $projects = $this->getProjectmapper()->findBy(array('fkaccountid' => $account, 'state' => 1), array('projectid' => 'DESC'));            
        }
        

        
        $projectlabels = array();
        $projectlabelchecked = array();

        // fetch labels
        foreach ( $projects as $project ) {
            $projectid = $project->getProjectid();
            $labels = $this->getLabelmapper()->findBy( array('fkaccountid' => $account, 'state' => 1, 'fkProjectid' => $project->getProjectid()), array('labelid' => 'ASC'));           
            $projectlabels[ $projectid ] = $labels;     
        } 
        $timeregs = null;
        
        $form = new ReportForm($this->getEntityManager());
        $form->get('choices')->setValueOptions( array("Please select", "Year","Month","week", "Quater","Period"));
        $data = $request->getPost();
        $projectsparam = $data['projectcheckbox']; 
        $projectlabelsparam = $data['projectlabelcheckbox']; 

        $year = $data['year'];   
      
        $projectchecked = array();
        if ($request->isPost() ) {           
            $projectwhere = "";
            $projectlabelwhere = "";
            $counter = 0;    
            $year = (new DateTime())->format('Y');
            if (isset($projectsparam)) {
                // query based on project choosen
                foreach ( $projectsparam as $project ) {
                    $method = 'or';
                    if ($counter==0) {
                        $counter++;
                        $method = 'and (';                    
                    }                
                    $projectwhere .= $method . " p.projectname = '" . $project . "' ";
                    $projectchecked[$project] = true; 
                }  
                $projectwhere .= ')';
                if (strlen($projectwhere)<2) {
                    $projectwhere = "";
                }            
            }
            $counter = 0;
            
            if (isset($projectlabelsparam)) {
                // query based on projectlabels chosen
                foreach ( $projectlabelsparam as $projectlabel ) {
                    $method = 'or';
                    if ($counter==0) {
                        $counter++;
                        $method = 'and (';                    
                    }                
                    $projectlabelwhere .= $method . " l.labelname = '" . trim($projectlabel) . "' ";
                    $projectlabelchecked[trim($projectlabel)] = true; 
                }  
                $projectlabelwhere .= ')';                

                if (strlen($projectlabelwhere)<2) {
                    $projectlabelwhere = "";
                }
            }
            
            $timelimiting = $this->getTimeQuery($form, $data);
            
            // søg resultater
            $textquery =    'SELECT tr, to, t, p '
                            . 'FROM Fluxuser\Entity\Timereg tr '
                            . 'JOIN tr.fktaskownerid to '
                            . 'JOIN to.fktaskid t '
                            . 'JOIN t.fkprojectid p '
                            . 'JOIN t.fklabelid l '
                            . 'JOIN to.fkuserid u '
                            . "where "
                            . "t.state = 1 "           
                            . $timelimiting
                            . $projectwhere                            
                            . $projectlabelwhere                            
                            . " order by t.status desc";
            $query = $this->getEntityManager()->createQuery( $textquery);
            $timeregs = $query->getResult();
            
            // save content to file
            if (isset($data['submitcvs'])) {               
                // create unique filename of time username and userreport
                $currentdatetime = new DateTime();
                $userid = $this->identity()->getUsername();
                $filename = 'Projectreport_' . $userid . '-' . $currentdatetime->format("Y-m-d-H-i-s") . '.csv';
               
                // create array of rows for the csv
                $myarray = array();
                foreach( $timeregs as $timereg ) {
                    // beregn total diff i timeregistration
                    $from = $timereg->getTimestart();
                    $to = $timereg->getTimestop();
                    $diff = $to->getTimestamp() - $from->getTimestamp();
                    $difftime = floor($diff / 3600) . ':' . floor(($diff / 60) % 60) . ':' . $diff % 60;
                    
                    // define row in csvfile
                        $row[] =    array(      'Taskid' => $timereg->getFktaskownerid()->getFktaskid()->getTaskid(),
                                    'Taskname' => $timereg->getFktaskownerid()->getFktaskid()->getTaskname(),
                                    'Client' => $timereg->getFktaskownerid()->getFktaskid()->getFkprojectid()->getFkclientid()->getClientname(),
                                    'Project' => $timereg->getFktaskownerid()->getFktaskid()->getFkprojectid()->getProjectname(),
                                    'Label' => $timereg->getFktaskownerid()->getFktaskid()->getFklabelid()->getLabelname(),
                                    'Username' => $timereg->getFktaskownerid()->getFkuserid()->getUsername(),
                                    'Imported' => $timereg->getFktaskownerid()->getFktaskid()->getFkprojectid()->getSecondid(),
                                    'Fra' => $timereg->getTimestart()->format("Y-m-d H-i-s"),
                                    'Til' => $timereg->getTimestop()->format("Y-m-d H-i-s"),
                                    'Samlet' => $difftime
                        );   
                }
                return $this->csvAction($filename, $row);
            }
        }

        // weeks list of current year
        $totalweeksinyear = $this->getWeeksInYear($year);
        $weeks = $this->getWeeks($totalweeksinyear);
        $form->get('week')->setValueOptions( $weeks );
        
        // years 
        $years = $this->getYears();
        $form->get('year')->setValueOptions( $years ); 
        
        // kvartal
        $form->get('quater')->setValueOptions( $this->getQuaternames() );   
        
        // month
        $month_names = $this->getMonthnames();
        $form->get('month')->setValueOptions($month_names );  
        
        // values to the view
        return array('form' => $form, "projects" => $projects, "projectlabels" => $projectlabels, "timeregs" => $timeregs, 'projectchecked' => $projectchecked, 'projectlabelchecked' => $projectlabelchecked);
    }
    
    /**
     *  user reports
     * admin
     * @return type
     */
    public function userAction() {
        //På siden report 
        
        //Henter sidens request 
        $request = $this->getRequest();
        
        $account = $this->identity()->getFkaccountid();
        $users = $this->getUsermapper()->findBy(array('fkaccountid' => $account, 'state' => 1), array('id' => 'ASC'));
        $timeregs = null;
        
        $form = new ReportForm($this->getEntityManager());
        $form->get('choices')->setValueOptions( array("Please select", "Year", "Month", "Week", "Quater", "Period"));
        $data = $request->getPost();
        $usersparam = $data['usercheckbox'];   
      
        $userchecked = array();
        if ($request->isPost() && isset($usersparam) ) {           
            $userwhere = "";
            $counter = 0;    
            $year = (new DateTime())->format('Y');
            foreach ( $usersparam as $user ) {
                $method = 'or';
                if ($counter==0) {
                    $counter++;
                    $method = 'and (';                    
                }                
                $userwhere .= $method . " u.username = '" . $user . "' ";
                $userchecked[$user] = true; 
            }  
            $userwhere .= ')';
            if (strlen($userwhere)<2) {
                $userwhere = "";
            }
            
            $timelimiting = $this->getTimeQuery($form, $data);
            
            // søg resultater
            $textquery =    'SELECT tr, to, t, p '
                            . 'FROM Fluxuser\Entity\Timereg tr '
                            . 'JOIN tr.fktaskownerid to '
                            . 'JOIN to.fktaskid t '
                        
                            . 'JOIN t.fkprojectid p '
                            . 'JOIN t.fklabelid l '
                            . 'JOIN to.fkuserid u '
                            . "where "
                            . "t.state = 1 "           
                            . $timelimiting
                            . $userwhere                            
                            . " order by t.status desc";
            $query = $this->getEntityManager()->createQuery( $textquery);
            $timeregs = $query->getResult();
            
            // save content to file
            if (isset($data['submitcvs'])) {
                // create unique filename of time username and userreport
                $currentdatetime = new DateTime();
                $userid = $this->identity()->getUsername();
                $filename = 'Userreport_' . $userid . '-' . $currentdatetime->format("Y-m-d-H-i-s") . '.csv';
                $filename = 'Userreport.csv';
               
                // create array of rows for the csv
                $myarray = array();
                foreach( $timeregs as $timereg ) {
                    // beregn total diff i timeregistration
                    $from = $timereg->getTimestart();
                    $to = $timereg->getTimestop();
                    $diff = $to->getTimestamp() - $from->getTimestamp();
                    $difftime = floor($diff / 3600) . ':' . floor(($diff / 60) % 60) . ':' . $diff % 60;
                    
                    // define row in csvfile
                    $row[] =    array(      'Taskid' => $timereg->getFktaskownerid()->getFktaskid()->getTaskid(),
                                    'Taskname' => $timereg->getFktaskownerid()->getFktaskid()->getTaskname(),
                                    'Client' => $timereg->getFktaskownerid()->getFktaskid()->getFkprojectid()->getFkclientid()->getClientname(),
                                    'Project' => $timereg->getFktaskownerid()->getFktaskid()->getFkprojectid()->getProjectname(),
                                    'Label' => $timereg->getFktaskownerid()->getFktaskid()->getFklabelid()->getLabelname(),
                                    'Username' => $timereg->getFktaskownerid()->getFkuserid()->getUsername(),
                                    'Imported' => $timereg->getFktaskownerid()->getFktaskid()->getFkprojectid()->getSecondid(),
                                    'Fra' => $timereg->getTimestart()->format("Y-m-d H-i-s"),
                                    'Til' => $timereg->getTimestop()->format("Y-m-d H-i-s"),
                                    'Samlet' => $difftime
                        );                
                }

                return $this->csvAction($filename, $row);
            }

        }

        // weeks list of current year
        $totalweeksinyear = $this->getWeeksInYear($year);
        $weeks = $this->getWeeks($totalweeksinyear);
        $form->get('week')->setValueOptions( $weeks );
        
        // years 
        $years = $this->getYears();
        $form->get('year')->setValueOptions( $years ); 
        
        // kvartal
        $form->get('quater')->setValueOptions( $this->getQuaternames() );   
        
        // month
        $month_names = $this->getMonthnames();
        $form->get('month')->setValueOptions($month_names );   
            
        // values to the view
        return array('form' => $form, "users" => $users, "timeregs" => $timeregs, 'userchecked' => $userchecked );
    }
    
    /**
     * Uses the forms timechoices to calculate a query string
     * @param type $form
     * @param type $data
     * @return type
     */
    private function getTimeQuery($form, $data) {
            $timelimiting = '';
            $form->get('choices')->setValue($data['choices']);
            switch($data['choices']) {
                case '1':
                    // year                    
                   $timelimiting = $this->getYearQuery($form, $data);                    
                break;
                case '2':
                    // year and month                   
                    $timelimiting = $this->getYearAndMonthQuery($form, $data);     
                break;
                case '3':
                    // year and week
                    $timelimiting = $this->getYearAndWeekQuery($form, $data); 
                break;
                case '4':
                    // year and quater
                     $timelimiting = $this->getYearAndQuaterQuery($form, $data); 
                break;
                case '5':                    
                    // to and from
                    $timelimiting = $this->getFromToQuery($form, $data); 
                break;
            }
            return $timelimiting;
    }
    
    /**
     * Get query by YEAR
     * @param type $form
     * @param type $data
     * @return string
     */
    private function getYearQuery($form, $data) { 
        $timelimiting = '';
        $year = $data['year'];
        if (isset($year)) {
            $timelimiting = " and tr.timestart > '" . $year . "-01-01 00:00:00'" ;
            $timelimiting .= " and tr.timestop < '" . ($year+1) . "-01-01 00:00:00' ";
            // set selected in form
            $form->get('year')->setValue($year);
        }                           
        return $timelimiting;
    }
    
    
    /**
     * Build query of year and month
     * @param type $form
     * @param type $data
     * @return string
     */
    private function getYearAndMonthQuery($form, $data) {
        $timelimiting = '';
        $year = $data['year'];
         $toyear = $year;
         $month = $data['month'];

         // korreger hvis sidste måned til nyt når
         if ($month==12) {
             $frommonth = '12';
             $tomonth = '01';
             $toyear = $year + 1;
         }

         // korreger til korrekt formateret måned fra 2014-1 til 2014-01
         else if (iconv_strlen($month+"")==1) {
             $frommonth = '0' . $month;
             $tomonth = '0' . ($month+1);
         } 
         // hvis måned er over 9
         else {
             $frommonth = $month;
             $tomonth = ($month+1);
         }

         // check hvis både år og måned er udfyldt lav query teksten
         if ( isset($year) && isset($month) ) {
             $timelimiting = " and tr.timestart > '" . $year . "-" . $frommonth . "-01 00:00:00'" ;
             $timelimiting .= " and tr.timestop < '" . $toyear . "-" . $tomonth . "-01 00:00:00' " ;
         }

         // set selected in form
         $form->get('year')->setValue($year);
         $form->get('month')->setValue($month);
        return $timelimiting;        
    }
    
    /**
     * Build query by year and week
     * @param type $form
     * @param type $data
     * @return string
     */
    private function getYearAndWeekQuery($form, $data) {   
        $timelimiting = '';
        $year = $data['year'];                    
        $week = $data['week'];

        // calculate new to- and fromdate by week
        $correctedfromweek = $week;
        $correctedtoweek = ($week+1);
        // correct week to be used in strtotime need format 04 for fourth week
        if (iconv_strlen($correctedfromweek+"")==1) {
            $correctedfromweek = '0' . $correctedfromweek;
        } 
        if (iconv_strlen($correctedtoweek+"")==1) {
            $correctedtoweek = '0' . $correctedtoweek;
        } 

        $startdate = date('Y-m-d',strtotime($year . 'W' . $correctedfromweek ));
        $stopdate = date('Y-m-d',strtotime($year . 'W' . $correctedtoweek  ));

        // create query
        $timelimiting = " and tr.timestart > '" . $startdate . " 00:00:00'";
        $timelimiting .= " and tr.timestop < '" . $stopdate . " 00:00:00' ";

        // set selected in form
        $form->get('year')->setValue($year);
        $form->get('week')->setValue($week);
        
        return $timelimiting;        
    }
    
    /**
     * Build query by year and quater (time of year 1. - 4.)
     * @param type $form
     * @param type $data
     * @return string
     */
    private function getYearAndQuaterQuery($form, $data) {   
        $timelimiting = '';
        $year = $data['year'];       
        $quater = $data['quater'] + 1;

        $startmonth = (($quater * 3) - 2);
        if ( iconv_strlen($startmonth+"")==1 ) { 
            $startmonth = '0'. $startmonth;                         
        }

        $stopmonth = ($quater * 3);
        if ( iconv_strlen($stopmonth+"")==1 ) { 
            $stopmonth = '0'. $stopmonth;                         
        }

        $startday = '01';
        $stopday = cal_days_in_month(CAL_GREGORIAN, $stopmonth, $year); 

        // correct stop day to be used in date needs format 04 for fourth day
        if ( iconv_strlen($stopday+"")==1 ) { 
            $stopday = '0' . $stopday;                         
        } 
        $startdate = $year . "-" . $startmonth . "-" . $startday;
        $stopdate = $year . "-" . $stopmonth . "-" . $stopday;
        // create query
        $timelimiting = " and tr.timestart > '" . $startdate . " 00:00:00'";
        $timelimiting .= " and tr.timestop < '" . $stopdate . " 00:00:00' ";

        // set selected in form
        $form->get('year')->setValue($year);
        $form->get('quater')->setValue(($quater-1));
        return $timelimiting;     
    }
    
    /**
     * Build query by from date and to date
     * @param type $form
     * @param type $data
     * @return string
     */
    private function getFromToQuery($form, $data) {        
        $timelimiting = '';
        $startdate = $data['from'];
        $stopdate = $data['to'];                    
        // create query
        $timelimiting = " and tr.timestart > '" . $startdate . ":00'";
        $timelimiting .= " and tr.timestop < '" . $stopdate . ":00' ";

        // set selected in form
        $form->get('from')->setValue($startdate);                       
        $form->get('to')->setValue($stopdate);                     
        
        return $timelimiting;             
    }
        
    /**
     * Building csv file as a view
     * @param type $filename
     * @param type $resultset
     * @return type
     */
    protected function csvAction($filename, $resultset) {
        $view = new ViewModel();
        $view->setTemplate('download/download-csv')
                ->setVariable('results', $resultset)
                ->setTerminal(true);
        $output = $this->getServiceLocator()->get('viewrenderer')->render($view);
        $response = $this->getResponse();

        // indstil headers
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'text/csv')
                ->addHeaderLine('Content-Disposition', sprintf("attachment; filename=\"%s\"", $filename))
                ->addHeaderLine('Accept-Ranges', 'bytes')
                ->addHeaderLine('Content-Length', strlen($output));
        $response->setContent($output);
        return $response;
    }
    
    /**
     * Get the correct weeks in year by iso standards
     * @param type $year
     * @return type
     */
    private function getWeeksInYear( $year ) {
        // create current date
        $date = new DateTime;
        $date->setISODate($year, 53);
        // returns only 52 if result == 01 and 52
        return ($date->format("W") === "53" ? 53 : 52);
    }
   
    /**
     * // pretyped month names
     * @return type
     */
    private function getMonthnames() {
        return array(1=>"JAN",2=>"FEB",3=>"MAR",4=>"APR",5=>"MAJ",6=>"JUN",7=>"JUL",8=>"AUG",9=>"SEP",10=>"OKT",11=>"NOV",12=>"DEC");
    }
    
    /**
     * Get pretyped Quater names
     * @return type
     */
    private function getQuaternames() {
        return array("1st Quarter", "2nd Quarter", "3rd Quarter", "4th Quarter");
    }
    
    /**
     * Get the start year of this system to 2 years from now
     * @return int
     */
    private function getYears() {
        $startyear = 2014;
        $currentyear = (new DateTime())->format('Y');
       
        for ($year = $startyear; $year < $currentyear+3; $year ++ ) {
            $years[$year] = $year;
        }
        return $years;
    }
    
    /**
     * Get list of weeks depending on the year
     * @param type $totalweeksinyear
     * @return int
     */
    private function getWeeks($totalweeksinyear) {
        for ($week = 1; $week < $totalweeksinyear+1; $week ++ ) {
            $weeks[$week] = $week;
        }
        return $weeks;
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

