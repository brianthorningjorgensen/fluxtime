<?php

namespace Fluxuser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Taskowner
 *
 * @ORM\Table(name="taskowner", uniqueConstraints={@ORM\UniqueConstraint(name="taskowner_fktaskid_fkuserid_key", columns={"fktaskid", "fkuserid"})}, indexes={@ORM\Index(name="IDX_E1F6021CBAE7A687", columns={"fkuserid"}), @ORM\Index(name="IDX_E1F6021CD1E217C", columns={"fktaskid"}), @ORM\Index(name="IDX_E1F6021CEBB291F4", columns={"fkaccountid"})})
 * @ORM\Entity
 */
class Taskowner
{
    /**
     * @var integer
     *
     * @ORM\Column(name="taskownerid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="taskowner_taskownerid_seq", allocationSize=1, initialValue=1)
     */
    private $taskownerid;

    /**
     * @var \Fluxuser\Entity\FluxUser
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\FluxUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fkuserid", referencedColumnName="id")
     * })
     */
    private $fkuserid;

    /**
     * @var \Fluxuser\Entity\Task
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\Task")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fktaskid", referencedColumnName="taskid")
     * })
     */
    private $fktaskid;

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
     * Get taskownerid
     *
     * @return integer 
     */
    public function getTaskownerid()
    {
        return $this->taskownerid;
    }

    /**
     * Set fkuserid
     *
     * @param \Fluxuser\Entity\FluxUser $fkuserid
     * @return Taskowner
     */
    public function setFkuserid(\Fluxuser\Entity\FluxUser $fkuserid = null)
    {
        $this->fkuserid = $fkuserid;

        return $this;
    }

    /**
     * Get fkuserid
     *
     * @return \Fluxuser\Entity\FluxUser 
     */
    public function getFkuserid()
    {
        return $this->fkuserid;
    }

    /**
     * Set fktaskid
     *
     * @param \Fluxuser\Entity\Task $fktaskid
     * @return Taskowner
     */
    public function setFktaskid(\Fluxuser\Entity\Task $fktaskid = null)
    {
        $this->fktaskid = $fktaskid;

        return $this;
    }

    /**
     * Get fktaskid
     *
     * @return \Fluxuser\Entity\Task 
     */
    public function getFktaskid()
    {
        return $this->fktaskid;
    }

    /**
     * Set fkaccountid
     *
     * @param \Fluxuser\Entity\Systemaccount $fkaccountid
     * @return Taskowner
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
