<?php

namespace Fluxuser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FluxUser
 *
 * @ORM\Table(name="flux_user", uniqueConstraints={@ORM\UniqueConstraint(name="flux_user_username_fkaccountid_key", columns={"username", "fkaccountid"}), @ORM\UniqueConstraint(name="flux_user_work_email_key", columns={"work_email"})}, indexes={@ORM\Index(name="IDX_181781B75732E4A", columns={"fkuserrole"}), @ORM\Index(name="IDX_181781BEBB291F4", columns={"fkaccountid"})})
 * @ORM\Entity
 */
class FluxUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="flux_user_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="employee_id", type="string", length=30, nullable=true)
     */
    private $employeeId;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=40, nullable=false)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=40, nullable=false)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="private_email", type="string", length=50, nullable=true)
     */
    private $privateEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="work_email", type="string", length=50, nullable=false)
     */
    private $workEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=20, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=72, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=50, nullable=true)
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="house_number", type="string", length=10, nullable=true)
     */
    private $houseNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=50, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="zip_code", type="string", length=10, nullable=true)
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=50, nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_private", type="string", length=20, nullable=true)
     */
    private $phonePrivate;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer", nullable=false)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="pivotaltrackerapi", type="string", length=255, nullable=true)
     */
    private $pivotaltrackerapi;

    /**
     * @var \Fluxuser\Entity\Usergroup
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\Usergroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fkuserrole", referencedColumnName="id")
     * })
     */
    private $fkuserrole;

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
     * Set employeeId
     *
     * @param string $employeeId
     * @return FluxUser
     */
    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    /**
     * Get employeeId
     *
     * @return string 
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return FluxUser
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return FluxUser
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return FluxUser
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set privateEmail
     *
     * @param string $privateEmail
     * @return FluxUser
     */
    public function setPrivateEmail($privateEmail)
    {
        $this->privateEmail = $privateEmail;

        return $this;
    }

    /**
     * Get privateEmail
     *
     * @return string 
     */
    public function getPrivateEmail()
    {
        return $this->privateEmail;
    }

    /**
     * Set workEmail
     *
     * @param string $workEmail
     * @return FluxUser
     */
    public function setWorkEmail($workEmail)
    {
        $this->workEmail = $workEmail;

        return $this;
    }

    /**
     * Get workEmail
     *
     * @return string 
     */
    public function getWorkEmail()
    {
        return $this->workEmail;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return FluxUser
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return FluxUser
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return FluxUser
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set houseNumber
     *
     * @param string $houseNumber
     * @return FluxUser
     */
    public function setHouseNumber($houseNumber)
    {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    /**
     * Get houseNumber
     *
     * @return string 
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return FluxUser
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     * @return FluxUser
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string 
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return FluxUser
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set phonePrivate
     *
     * @param string $phonePrivate
     * @return FluxUser
     */
    public function setPhonePrivate($phonePrivate)
    {
        $this->phonePrivate = $phonePrivate;

        return $this;
    }

    /**
     * Get phonePrivate
     *
     * @return string 
     */
    public function getPhonePrivate()
    {
        return $this->phonePrivate;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return FluxUser
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
     * Set pivotaltrackerapi
     *
     * @param string $pivotaltrackerapi
     * @return FluxUser
     */
    public function setPivotaltrackerapi($pivotaltrackerapi)
    {
        $this->pivotaltrackerapi = $pivotaltrackerapi;

        return $this;
    }

    /**
     * Get pivotaltrackerapi
     *
     * @return string 
     */
    public function getPivotaltrackerapi()
    {
        return $this->pivotaltrackerapi;
    }

    /**
     * Set fkuserrole
     *
     * @param \Fluxuser\Entity\Usergroup $fkuserrole
     * @return FluxUser
     */
    public function setFkuserrole(\Fluxuser\Entity\Usergroup $fkuserrole = null)
    {
        $this->fkuserrole = $fkuserrole;

        return $this;
    }

    /**
     * Get fkuserrole
     *
     * @return \Fluxuser\Entity\Usergroup 
     */
    public function getFkuserrole()
    {
        return $this->fkuserrole;
    }

    /**
     * Set fkaccountid
     *
     * @param \Fluxuser\Entity\Systemaccount $fkaccountid
     * @return FluxUser
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
