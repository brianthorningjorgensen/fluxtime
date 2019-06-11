<?php

namespace Fluxuser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Urlresource
 *
 * @ORM\Table(name="urlresource", uniqueConstraints={@ORM\UniqueConstraint(name="urlresource_urlresource_key", columns={"urlresource"})})
 * @ORM\Entity
 */
class Urlresource
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="urlresource_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="urlresource", type="string", length=40, nullable=false)
     */
    private $urlresource;



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
     * Set urlresource
     *
     * @param string $urlresource
     * @return Urlresource
     */
    public function setUrlresource($urlresource)
    {
        $this->urlresource = $urlresource;

        return $this;
    }

    /**
     * Get urlresource
     *
     * @return string 
     */
    public function getUrlresource()
    {
        return $this->urlresource;
    }
}
