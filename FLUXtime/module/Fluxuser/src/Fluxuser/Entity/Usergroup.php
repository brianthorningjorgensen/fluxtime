<?php

namespace Fluxuser\Entity;
 
use Doctrine\ORM\Mapping as ORM;

/**
 * Usergroup
 *
 * @ORM\Table(name="usergroup", uniqueConstraints={@ORM\UniqueConstraint(name="usergroup_permissiongroup_key", columns={"permissiongroup"})})
 * @ORM\Entity
 */
class Usergroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="usergroup_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="permissiongroup", type="string", length=30, nullable=false)
     */
    private $permissiongroup;



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
     * Set permissiongroup
     *
     * @param string $permissiongroup
     * @return Usergroup
     */
    public function setPermissiongroup($permissiongroup)
    {
        $this->permissiongroup = $permissiongroup;

        return $this;
    }

    /**
     * Get permissiongroup
     *
     * @return string 
     */
    public function getPermissiongroup()
    {
        return $this->permissiongroup;
    }
}
