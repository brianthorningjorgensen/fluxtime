<?php

namespace Fluxuser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 *
 * @ORM\Table(name="task", indexes={@ORM\Index(name="IDX_527EDB25659B6570", columns={"fkcreator"}), @ORM\Index(name="IDX_527EDB25E5FF42EB", columns={"fkprojectid"}), @ORM\Index(name="IDX_527EDB259B68CEF4", columns={"fklabelid"}), @ORM\Index(name="IDX_527EDB25EBB291F4", columns={"fkaccountid"})})
 * @ORM\Entity
 */
class Task
{
    /**
     * @var integer
     *
     * @ORM\Column(name="taskid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="task_taskid_seq", allocationSize=1, initialValue=1)
     */
    private $taskid;

    /**
     * @var string
     *
     * @ORM\Column(name="secondid", type="string", length=30, nullable=true)
     */
    private $secondid;

    /**
     * @var string
     *
     * @ORM\Column(name="taskname", type="string", length=255, nullable=false)
     */
    private $taskname;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer", nullable=false)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20, nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="points", type="string", length=30, nullable=true)
     */
    private $points;

    /**
     * @var string
     *
     * @ORM\Column(name="tasktype", type="string", length=30, nullable=true)
     */
    private $tasktype;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \Fluxuser\Entity\FluxUser
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\FluxUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fkcreator", referencedColumnName="id")
     * })
     */
    private $fkcreator;

    /**
     * @var \Fluxuser\Entity\Project
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fkprojectid", referencedColumnName="projectid")
     * })
     */
    private $fkprojectid;

    /**
     * @var \Fluxuser\Entity\Projectlabel
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\Projectlabel")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fklabelid", referencedColumnName="labelid")
     * })
     */
    private $fklabelid;

    /**
     * @var \Fluxuser\Entity\Systemaccount
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\Systemaccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fkaccountid", referencedColumnName="accountid")
     * })
     */
    private $fkaccountid;



    /**
     * Get taskid
     *
     * @return integer 
     */
    public function getTaskid()
    {
        return $this->taskid;
    }

    /**
     * Set secondid
     *
     * @param string $secondid
     * @return Task
     */
    public function setSecondid($secondid)
    {
        $this->secondid = $secondid;

        return $this;
    }

    /**
     * Get secondid
     *
     * @return string 
     */
    public function getSecondid()
    {
        return $this->secondid;
    }

    /**
     * Set taskname
     *
     * @param string $taskname
     * @return Task
     */
    public function setTaskname($taskname)
    {
        $this->taskname = $taskname;

        return $this;
    }

    /**
     * Get taskname
     *
     * @return string 
     */
    public function getTaskname()
    {
        return $this->taskname;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Task
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set points
     *
     * @param string $points
     * @return Task
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return string 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set tasktype
     *
     * @param string $tasktype
     * @return Task
     */
    public function setTasktype($tasktype)
    {
        $this->tasktype = $tasktype;

        return $this;
    }

    /**
     * Get tasktype
     *
     * @return string 
     */
    public function getTasktype()
    {
        return $this->tasktype;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set fkcreator
     *
     * @param \Fluxuser\Entity\FluxUser $fkcreator
     * @return Task
     */
    public function setFkcreator(\Fluxuser\Entity\FluxUser $fkcreator = null)
    {
        $this->fkcreator = $fkcreator;

        return $this;
    }

    /**
     * Get fkcreator
     *
     * @return \Fluxuser\Entity\FluxUser 
     */
    public function getFkcreator()
    {
        return $this->fkcreator;
    }

    /**
     * Set fkprojectid
     *
     * @param \Fluxuser\\Entity\Project $fkprojectid
     * @return Task
     */
    public function setFkprojectid(\Fluxuser\Entity\Project $fkprojectid = null)
    {
        $this->fkprojectid = $fkprojectid;

        return $this;
    }

    /**
     * Get fkprojectid
     *
     * @return \Fluxuser\\Entity\Project 
     */
    public function getFkprojectid()
    {
        return $this->fkprojectid;
    }

    /**
     * Set fklabelid
     *
     * @param \Fluxuser\Entity\Projectlabel $fklabelid
     * @return Task
     */
    public function setFklabelid(\Fluxuser\Entity\Projectlabel $fklabelid = null)
    {
        $this->fklabelid = $fklabelid;

        return $this;
    }

    /**
     * Get fklabelid
     *
     * @return \Fluxuser\Entity\Projectlabel 
     */
    public function getFklabelid()
    {
        return $this->fklabelid;
    }

    /**
     * Set fkaccountid
     *
     * @param \Fluxuser\Entity\Systemaccount $fkaccountid
     * @return Task
     */
    public function setFkaccountid(\Fluxuser\Entity\Systemaccount $fkaccountid = null)
    {
        $this->fkaccountid = $fkaccountid;

        return $this;
    }

    /**
     * Get fkaccountid
     *
     * @return \Fluxuser\Entity\Systemaccount 
     */
    public function getFkaccountid()
    {
        return $this->fkaccountid;
    }
}
