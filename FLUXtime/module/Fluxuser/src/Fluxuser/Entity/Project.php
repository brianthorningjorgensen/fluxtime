<?php

namespace Fluxuser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="project", uniqueConstraints={@ORM\UniqueConstraint(name="project_projectname_fkaccountid_key", columns={"projectname", "fkaccountid"})}, indexes={@ORM\Index(name="IDX_2FB3D0EE61BDC099", columns={"fk_projectmanager"}), @ORM\Index(name="IDX_2FB3D0EEEBB291F4", columns={"fkaccountid"}), @ORM\Index(name="IDX_2FB3D0EEFBFF114D", columns={"fkclientid"})})
 * @ORM\Entity
 */
class Project
{
    /**
     * @var integer
     *
     * @ORM\Column(name="projectid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="project_projectid_seq", allocationSize=1, initialValue=1)
     */
    private $projectid;

    /**
     * @var string
     *
     * @ORM\Column(name="projectname", type="string", length=50, nullable=false)
     */
    private $projectname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdate", type="date", nullable=false)
     */
    private $createdate;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer", nullable=false)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="secondid", type="string", length=30, nullable=true)
     */
    private $secondid;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="integer", nullable=false)
     */
    private $active;

    /**
     * @var \Fluxuser\Entity\FluxUser
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\FluxUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_projectmanager", referencedColumnName="id")
     * })
     */
    private $fkProjectmanager;

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
     * @var \Fluxuser\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fkclientid", referencedColumnName="clientid")
     * })
     */
    private $fkclientid;



    /**
     * Get projectid
     *
     * @return integer 
     */
    public function getProjectid()
    {
        return $this->projectid;
    }

    /**
     * Set projectname
     *
     * @param string $projectname
     * @return Project
     */
    public function setProjectname($projectname)
    {
        $this->projectname = $projectname;

        return $this;
    }

    /**
     * Get projectname
     *
     * @return string 
     */
    public function getProjectname()
    {
        return $this->projectname;
    }

    /**
     * Set createdate
     *
     * @param \DateTime $createdate
     * @return Project
     */
    public function setCreatedate($createdate)
    {
        $this->createdate = $createdate;

        return $this;
    }

    /**
     * Get createdate
     *
     * @return \DateTime 
     */
    public function getCreatedate()
    {
        return $this->createdate;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Project
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
     * Set secondid
     *
     * @param string $secondid
     * @return Project
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
     * Set active
     *
     * @param integer $active
     * @return Project
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set fkProjectmanager
     *
     * @param \Fluxuser\Entity\FluxUser $fkProjectmanager
     * @return Project
     */
    public function setFkProjectmanager(\Fluxuser\Entity\FluxUser $fkProjectmanager = null)
    {
        $this->fkProjectmanager = $fkProjectmanager;

        return $this;
    }

    /**
     * Get fkProjectmanager
     *
     * @return \Fluxuser\Entity\FluxUser 
     */
    public function getFkProjectmanager()
    {
        return $this->fkProjectmanager;
    }

    /**
     * Set fkaccountid
     *
     * @param \Fluxuser\Entity\Systemaccount $fkaccountid
     * @return Project
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

    /**
     * Set fkclientid
     *
     * @param \Fluxuser\Entity\Client $fkclientid
     * @return Project
     */
    public function setFkclientid(\Fluxuser\Entity\Client $fkclientid = null)
    {
        $this->fkclientid = $fkclientid;

        return $this;
    }

    /**
     * Get fkclientid
     *
     * @return \Fluxuser\Entity\Client 
     */
    public function getFkclientid()
    {
        return $this->fkclientid;
    }
}
