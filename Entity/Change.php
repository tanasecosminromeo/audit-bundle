<?php

namespace TCR\AuditBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Change
 *
 * @ORM\Table(name="audit_changes",
    indexes={
           	@ORM\Index(name="field_idx", columns={"field"}, options={"lengths": [1000]})
    }))
 * @ORM\Entity
 */
class Change
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
     * @ORM\ManyToOne(targetEntity="Audit",cascade={"persist"},inversedBy="changes")
     */
    private $audit;

		/**
     * @var string
     *
     * @ORM\Column(name="field", type="text", nullable=true)
     */
    private $field;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;

		/**
		* Constructor
		*/
		public function __construct(\TCR\AuditBundle\Entity\Audit $audit, $field=null, $value=null) {
			$this->audit = $audit;
			$this->field = $field;
			$this->value = $value;
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
     * Set value
     *
     * @param string $value
     * @return Change
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set field
     *
     * @param string $field
     * @return Change
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set audit
     *
     * @param \TCR\AuditBundle\Entity\Audit $audit
     * @return Change
     */
    public function setAudit(\TCR\AuditBundle\Entity\Audit $audit = null)
    {
        $this->audit = $audit;

        return $this;
    }

    /**
     * Get audit
     *
     * @return \TCR\AuditBundle\Entity\Audit
     */
    public function getAudit()
    {
        return $this->audit;
    }
}
