<?php

namespace Fluxuser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Projectlabel
 *
 * @ORM\Table(name="projectlabel", uniqueConstraints={@ORM\UniqueConstraint(name="projectlabel_fk_projectid_labelname_fkaccountid_key", columns={"fk_projectid", "labelname", "fkaccountid"})}, indexes={@ORM\Index(name="IDX_6A13E8EB984E93C5", columns={"fk_projectid"}), @ORM\Index(name="IDX_6A13E8EBEBB291F4", columns={"fkaccountid"})})
 * @ORM\Entity
 */
class Projectlabel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="labelid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="projectlabel_labelid_seq", allocationSize=1, initialValue=1)
     */
    private $labelid;

    /**
     * @var string
     *
     * @ORM\Column(name="secondid", type="string", length=30, nullable=true)
     */
    private $secondid;

    /**
     * @var string
     *
     * @ORM\Column(name="labelname", type="string", length=50, nullable=false)
     */
    private $labelname;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer", nullable=false)
     */
    private $state;

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
     * @var \Fluxuser\Entity\Systemaccount
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\Systemaccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fkaccountid", referencedColumnName="accountid")
     * })
     */
    private $fkaccountid;



    /**
     * Get labelid
     *
     * @return integer 
     */
    public function getLabelid()
    {
        return $this->labelid;
    }

    /**
     * Set secondid
     *
     * @param string $secondid
     * @return Projectlabel
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
     * Set labelname
     *
     * @param string $labelname
     * @return Projectlabel
     */
    public function setLabelname($labelname)
    {
        $this->labelname = $labelname;

        return $this;
    }

    /**
     * Get labelname
     *
     * @return string 
     */
    public function getLabelname()
    {
        return $this->labelname;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Projectlabel
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
     * Set fkProjectid
     *
     * @param \Fluxuser\Entity\Project $fkProjectid
     * @return Projectlabel
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
     * Set fkaccountid
     *
     * @param \Fluxuser\Entity\Systemaccount $fkaccountid
     * @return Projectlabel
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
