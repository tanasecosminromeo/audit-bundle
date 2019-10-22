<?php
namespace TCR\AuditBundle\EventListener;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use TCR\AuditBundle\Entity\Audit;
use TCR\AuditBundle\Entity\Change;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AuditService
{
    private $em;
    private $changes = [];
    /**
     * @var array|false|string
     */
    private $audit_classes;
    /**
     * @var array|false|string
     */
    private $audit_prefix;

    public function __construct()
    {
        $this->audit_classes = getenv('AUDIT_CLASSES') ?? $this->container->getParameter('audit_classes');
        $this->audit_prefix = getenv('AUDIT_PREFIX') ?? $this->container->getParameter('audit_prefix');

        if (gettype($this->audit_classes) !== 'array'){
            $this->audit_classes = explode(',', $this->audit_classes);
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
				$entityName = ClassUtils::getRealClass(get_class($entity));

				# We only audit the entities we are configured to audit
				if (!in_array($entityName, $this->audit_classes)) return;

				if (count($args->getEntityChangeSet())<=0) return;
				$this->em = $args->getEntityManager();

				$key = $entity->getId()."|".str_replace($this->audit_prefix, '', $entityName);
				if (!isset($this->changes[$key])){ $this->changes[$key] = []; }
				foreach ($args->getEntityChangeSet() as $field => $values){
					if ($values[0]===$values[1]) continue;
					$this->changes[$key][$field] = $this->formatValue($values[0]);
				}
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if (count($this->changes)==0) return;

				$user = $this->getUser();
				foreach ($this->changes as $key => $changes){
					list($entityId, $entityName) = explode("|", $key);

					$audit = new Audit($entityName, $entityId, $user);
					foreach ($changes as $field => $value){
						$change = new Change($audit, $field, $value);
						$this->em->persist($change);
						$audit->addChange($change);
					}
					$this->em->persist($audit);
				}

				$this->changes = [];
				$this->em->flush();
    }

		# Get the current username
		private function getUser(){
			$token = $this->container->get('security.token_storage')->getToken();
			if (gettype($token)==='object'){
				$user = $token->getUser();
				if (gettype($user)==='object'){
					return $user->getId();
				}
			}

			return 0;
		}

		private function formatValue($value){
			switch (gettype($value)){
				case "object":
					 	$class = get_class($value);

						if (strstr($class, '\Entity\\')){
							return $value->getId();
						} elseif (strstr($class, 'DateTime')){
							return date('Y-m-d H:i:s', $value->getTimestamp());
						} else {
							return $class;
						}
					break;
				default:
					return $value;
			}
		}
}
