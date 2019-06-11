<?php

namespace Fluxuser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Accountclient
 *
 * @ORM\Table(name="accountclient", indexes={@ORM\Index(name="IDX_7BF331B8FBFF114D", columns={"fkclientid"}), @ORM\Index(name="IDX_7BF331B8EBB291F4", columns={"fkaccountid"})})
 * @ORM\Entity
 */
class Accountclient
{
    /**
     * @var integer
     *
     * @ORM\Column(name="accountclientid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="accountclient_accountclientid_seq", allocationSize=1, initialValue=1)
     */
    private $accountclientid;

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
     * @var \Fluxuser\Entity\Systemaccount
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\Systemaccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fkaccountid", referencedColumnName="accountid")
     * })
     */
    private $fkaccountid;



    /**
     * Get accountclientid
     *
     * @return integer 
     */
    public function getAccountclientid()
    {
        return $this->accountclientid;
    }

    /**
     * Set fkclientid
     *
     * @param \Fluxuser\Entity\Client $fkclientid
     * @return Accountclient
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

    /**
     * Set fkaccountid
     *
     * @param \Fluxuser\Entity\Systemaccount $fkaccountid
     * @return Accountclient
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
