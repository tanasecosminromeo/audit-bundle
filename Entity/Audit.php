<?php

namespace TCR\AuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Audit
 *
 * @ORM\Table(name="audit",
    indexes={
           	@ORM\Index(name="entity_idx", columns={"entity"}, options={"lengths": [1000]}),
				    @ORM\Index(name="entityId_idx", columns={"entityId"}),
						@ORM\Index(name="user_idx", columns={"user"})
    }))
 * @ORM\Entity(repositoryClass="TCR\AuditBundle\Repository\AuditRepository")
 */
class Audit
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

		/**
     * @var string
     *
     * @ORM\Column(name="entity", type="text")
     */
    private $entity;

		/**
     * @var integer
     *
     * @ORM\Column(name="entityId", type="integer")
     */
    private $entityId;

    /**
     * @var integer
     *
     * @ORM\Column(name="user", type="integer")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Change", cascade={"persist"},mappedBy="audit")
     */
    private $changes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

	  /**
	  * Constructor
	  */
	  public function __construct($entity="", $entityId=0, $user=0) {
	    $this->created  = new \DateTime("now");
			$this->entity   = $entity;
			$this->entityId = $entityId;
			$this->user     = $user;
			$this->changes  = new \Doctrine\Common\Collections\ArrayCollection();
		}

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
     * Set user
     *
     * @param integer $user
     * @return Audit
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return integer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set entity
     *
     * @param string $entity
     * @return Audit
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Audit
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Add change
     *
     * @param \TCR\AuditBundle\Entity\Change $change
     * @return Audit
     */
    public function addChange(\TCR\AuditBundle\Entity\Change $change)
    {
        $this->changes[] = $change;

        return $this;
    }

    /**
     * Remove changes
     *
     * @param \TCR\AuditBundle\Entity\Change $change
     */
    public function removeChange(\TCR\AuditBundle\Entity\Change $change)
    {
        $this->changes->removeElement($change);
    }

    /**
     * Get changes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * Set entityId
     *
     * @param integer $entityId
     * @return Audit
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get entityId
     *
     * @return integer
     */
    public function getEntityId()
    {
        return $this->entityId;
    }
}
