<?php

namespace Fluxuser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Resourcepermission
 *
 * @ORM\Table(name="resourcepermission", indexes={@ORM\Index(name="IDX_51C6028568C7EA9", columns={"fkusergroup"}), @ORM\Index(name="IDX_51C6028E68F79DC", columns={"fkurlresource"})})
 * @ORM\Entity
 */
class Resourcepermission
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="resourcepermission_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \Fluxuser\Entity\Usergroup
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\Usergroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fkusergroup", referencedColumnName="id")
     * })
     */
    private $fkusergroup;

    /**
     * @var \Fluxuser\Entity\Urlresource
     *
     * @ORM\ManyToOne(targetEntity="Fluxuser\Entity\Urlresource")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fkurlresource", referencedColumnName="id")
     * })
     */
    private $fkurlresource;



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
     * Set fkusergroup
     *
     * @param \Fluxuser\Entity\Usergroup $fkusergroup
     * @return Resourcepermission
     */
    public function setFkusergroup(\Fluxuser\Entity\Usergroup $fkusergroup = null)
    {
        $this->fkusergroup = $fkusergroup;

        return $this;
    }

    /**
     * Get fkusergroup
     *
     * @return \Fluxuser\Entity\Usergroup 
     */
    public function getFkusergroup()
    {
        return $this->fkusergroup;
    }

    /**
     * Set fkurlresource
     *
     * @param \Fluxuser\Entity\Urlresource $fkurlresource
     * @return Resourcepermission
     */
    public function setFkurlresource(\Fluxuser\Entity\Urlresource $fkurlresource = null)
    {
        $this->fkurlresource = $fkurlresource;

        return $this;
    }

    /**
     * Get fkurlresource
     *
     * @return \Fluxuser\Entity\Urlresource 
     */
    public function getFkurlresource()
    {
        return $this->fkurlresource;
    }
}
