<?php

namespace Fluxuser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Projectuser
 *
 * @ORM\Table(name="projectuser", uniqueConstraints={@ORM\UniqueConstraint(name="projectuser_fk_userid_fk_projectid_key", columns={"fk_userid", "fk_projectid"})}, indexes={@ORM\Index(name="IDX_52831550984E93C5", columns={"fk_projectid"}), @ORM\Index(name="IDX_52831550D19C01FE", columns={"fk_userid"}), @ORM\Index(name="IDX_52831550EBB291F4", columns={"fkaccountid"})})
 * @ORM\Entity
 */
class Projectuser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="projectuserid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="projectuser_projectuserid_seq", allocationSize=1, initialValue=1)
     */
    private $projectuserid;

    /**
     * @var \Fluxuser\Entity\Project
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_projectid", referencedColumnName="projectid")
     * })
     */
    private $fkProjectid;

    /**
     * @var \Fluxuser\Entity\FluxUser
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\FluxUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_userid", referencedColumnName="id")
     * })
     */
    private $fkUserid;

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
     * Get projectuserid
     *
     * @return integer 
     */
    public function getProjectuserid()
    {
        return $this->projectuserid;
    }

    /**
     * Set fkProjectid
     *
     * @param \Fluxuser\Entity\Project $fkProjectid
     * @return Projectuser
     */
    public function setFkProjectid(\Fluxuser\Entity\Project $fkProjectid = null)
    {
        $this->fkProjectid = $fkProjectid;

        return $this;
    }

    /**
     * Get fkProjectid
     *
     * @return \Fluxuser\Entity\Project 
     */
    public function getFkProjectid()
    {
        return $this->fkProjectid;
    }

    /**
     * Set fkUserid
     *
     * @param \Fluxuser\Entity\FluxUser $fkUserid
     * @return Projectuser
     */
    public function setFkUserid(\Fluxuser\Entity\FluxUser $fkUserid = null)
    {
        $this->fkUserid = $fkUserid;

        return $this;
    }

    /**
     * Get fkUserid
     *
     * @return \Fluxuser\Entity\FluxUser 
     */
    public function getFkUserid()
    {
        return $this->fkUserid;
    }

    /**
     * Set fkaccountid
     *
     * @param \Fluxuser\Entity\Systemaccount $fkaccountid
     * @return Projectuser
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
