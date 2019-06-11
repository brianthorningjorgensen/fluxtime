<?php

namespace Fluxuser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Projectcontact
 *
 * @ORM\Table(name="projectcontact", uniqueConstraints={@ORM\UniqueConstraint(name="projectcontact_fkcontactid_fkprojectid_key", columns={"fkcontactid", "fkprojectid"})}, indexes={@ORM\Index(name="IDX_A2F5EAF1E5FF42EB", columns={"fkprojectid"}), @ORM\Index(name="IDX_A2F5EAF1FD27D63A", columns={"fkcontactid"}), @ORM\Index(name="IDX_A2F5EAF1EBB291F4", columns={"fkaccountid"})})
 * @ORM\Entity
 */
class Projectcontact
{
    /**
     * @var integer
     *
     * @ORM\Column(name="projectcontactid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="projectcontact_projectcontactid_seq", allocationSize=1, initialValue=1)
     */
    private $projectcontactid;

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
     * @var \Fluxuser\Entity\Contact
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\Contact")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fkcontactid", referencedColumnName="contactid")
     * })
     */
    private $fkcontactid;

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
     * Get projectcontactid
     *
     * @return integer 
     */
    public function getProjectcontactid()
    {
        return $this->projectcontactid;
    }

    /**
     * Set fkprojectid
     *
     * @param \Fluxuser\Entity\Project $fkprojectid
     * @return Projectcontact
     */
    public function setFkprojectid(\Fluxuser\Entity\Project $fkprojectid = null)
    {
        $this->fkprojectid = $fkprojectid;

        return $this;
    }

    /**
     * Get fkprojectid
     *
     * @return \Fluxuser\Entity\Project 
     */
    public function getFkprojectid()
    {
        return $this->fkprojectid;
    }

    /**
     * Set fkcontactid
     *
     * @param \Fluxuser\Entity\Contact $fkcontactid
     * @return Projectcontact
     */
    public function setFkcontactid(\Fluxuser\Entity\Contact $fkcontactid = null)
    {
        $this->fkcontactid = $fkcontactid;

        return $this;
    }

    /**
     * Get fkcontactid
     *
     * @return \Fluxuser\Entity\Contact 
     */
    public function getFkcontactid()
    {
        return $this->fkcontactid;
    }

    /**
     * Set fkaccountid
     *
     * @param \Fluxuser\Entity\Systemaccount $fkaccountid
     * @return Projectcontact
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
