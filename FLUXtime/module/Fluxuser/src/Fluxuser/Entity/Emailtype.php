<?php

namespace Fluxuser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emailtype
 *
 * @ORM\Table(name="emailtype", uniqueConstraints={@ORM\UniqueConstraint(name="emailtype_emailtype_key", columns={"emailtype"})})
 * @ORM\Entity
 */
class Emailtype
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="emailtype_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="emailtype", type="string", length=50, nullable=false)
     */
    private $emailtype;



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
     * Set emailtype
     *
     * @param string $emailtype
     * @return Emailtype
     */
    public function setEmailtype($emailtype)
    {
        $this->emailtype = $emailtype;

        return $this;
    }

    /**
     * Get emailtype
     *
     * @return string 
     */
    public function getEmailtype()
    {
        return $this->emailtype;
    }
}
