<?php

namespace Fluxuser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Systemaccount
 *
 * @ORM\Table(name="systemaccount", uniqueConstraints={@ORM\UniqueConstraint(name="systemaccount_customer_key", columns={"customer"})})
 * @ORM\Entity
 */
class Systemaccount
{
    /**
     * @var integer
     *
     * @ORM\Column(name="accountid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="systemaccount_accountid_seq", allocationSize=1, initialValue=1)
     */
    private $accountid;

    /**
     * @var string
     *
     * @ORM\Column(name="customer", type="string", length=100, nullable=false)
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="customerid", type="string", length=50, nullable=true)
     */
    private $customerid;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="integer", nullable=false)
     */
    private $active;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer", nullable=false)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;



    /**
     * Get accountid
     *
     * @return integer 
     */
    public function getAccountid()
    {
        return $this->accountid;
    }

    /**
     * Set customer
     *
     * @param string $customer
     * @return Systemaccount
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return string 
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set customerid
     *
     * @param string $customerid
     * @return Systemaccount
     */
    public function setCustomerid($customerid)
    {
        $this->customerid = $customerid;

        return $this;
    }

    /**
     * Get customerid
     *
     * @return string 
     */
    public function getCustomerid()
    {
        return $this->customerid;
    }

    /**
     * Set active
     *
     * @param integer $active
     * @return Systemaccount
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
     * Set state
     *
     * @param integer $state
     * @return Systemaccount
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
     * Set description
     *
     * @param string $description
     * @return Systemaccount
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
}
