<?php

namespace Fluxuser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Timereg
 *
 * @ORM\Table(name="timereg", uniqueConstraints={@ORM\UniqueConstraint(name="timereg_fktaskownerid_timestart_key", columns={"fktaskownerid", "timestart"})}, indexes={@ORM\Index(name="IDX_5BC3163E2465803C", columns={"fktaskownerid"}), @ORM\Index(name="IDX_5BC3163EEBB291F4", columns={"fkaccountid"})})
 * @ORM\Entity
 */
class Timereg
{
    /**
     * @var integer
     *
     * @ORM\Column(name="timeregid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="timereg_timeregid_seq", allocationSize=1, initialValue=1)
     */
    private $timeregid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestart", type="datetime", nullable=false)
     */
    private $timestart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestop", type="datetime", nullable=true)
     */
    private $timestop;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer", nullable=false)
     */
    private $state;

    /**
     * @var \Fluxuser\Entity\Taskowner
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\Taskowner")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fktaskownerid", referencedColumnName="taskownerid")
     * })
     */
    private $fktaskownerid;

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
     * Get timeregid
     *
     * @return integer 
     */
    public function getTimeregid()
    {
        return $this->timeregid;
    }

    /**
     * Set timestart
     *
     * @param \DateTime $timestart
     * @return Timereg
     */
    public function setTimestart($timestart)
    {
        $this->timestart = $timestart;

        return $this;
    }

    /**
     * Get timestart
     *
     * @return \DateTime 
     */
    public function getTimestart()
    {
        return $this->timestart;
    }

    /**
     * Set timestop
     *
     * @param \DateTime $timestop
     * @return Timereg
     */
    public function setTimestop($timestop)
    {
        $this->timestop = $timestop;

        return $this;
    }

    /**
     * Get timestop
     *
     * @return \DateTime 
     */
    public function getTimestop()
    {
        return $this->timestop;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Timereg
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
     * Set fktaskownerid
     *
     * @param \Fluxuser\Entity\Taskowner $fktaskownerid
     * @return Timereg
     */
    public function setFktaskownerid(\Fluxuser\Entity\Taskowner $fktaskownerid = null)
    {
        $this->fktaskownerid = $fktaskownerid;

        return $this;
    }

    /**
     * Get fktaskownerid
     *
     * @return \Fluxuser\Entity\Taskowner 
     */
    public function getFktaskownerid()
    {
        return $this->fktaskownerid;
    }

    /**
     * Set fkaccountid
     *
     * @param \Fluxuser\Entity\Systemaccount $fkaccountid
     * @return Timereg
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
