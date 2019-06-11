<?php

namespace Fluxuser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Email
 *
 * @ORM\Table(name="email", indexes={@ORM\Index(name="IDX_E7927C74E6156830", columns={"userfk"}), @ORM\Index(name="IDX_E7927C74E221C7F", columns={"emailtypefk"}), @ORM\Index(name="IDX_E7927C74EBB291F4", columns={"fkaccountid"})})
 * @ORM\Entity
 */
class Email
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="email_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="senttime", type="datetime", nullable=false)
     */
    private $senttime;

    /**
     * @var \Fluxuser\Entity\FluxUser
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\FluxUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userfk", referencedColumnName="id")
     * })
     */
    private $userfk;

    /**
     * @var \Fluxuser\Entity\Emailtype
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\Emailtype")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emailtypefk", referencedColumnName="id")
     * })
     */
    private $emailtypefk;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set senttime
     *
     * @param \DateTime $senttime
     * @return Email
     */
    public function setSenttime($senttime)
    {
        $this->senttime = $senttime;

        return $this;
    }

    /**
     * Get senttime
     *
     * @return \DateTime 
     */
    public function getSenttime()
    {
        return $this->senttime;
    }

    /**
     * Set userfk
     *
     * @param \Fluxuser\Entity\FluxUser $userfk
     * @return Email
     */
    public function setUserfk(\Fluxuser\Entity\FluxUser $userfk = null)
    {
        $this->userfk = $userfk;

        return $this;
    }

    /**
     * Get userfk
     *
     * @return \Fluxuser\Entity\FluxUser 
     */
    public function getUserfk()
    {
        return $this->userfk;
    }

    /**
     * Set emailtypefk
     *
     * @param \Fluxuser\Entity\Emailtype $emailtypefk
     * @return Email
     */
    public function setEmailtypefk(\Fluxuser\Entity\Emailtype $emailtypefk = null)
    {
        $this->emailtypefk = $emailtypefk;

        return $this;
    }

    /**
     * Get emailtypefk
     *
     * @return \Fluxuser\Entity\Emailtype 
     */
    public function getEmailtypefk()
    {
        return $this->emailtypefk;
    }

    /**
     * Set fkaccountid
     *
     * @param \Fluxuser\Entity\Systemaccount $fkaccountid
     * @return Email
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
