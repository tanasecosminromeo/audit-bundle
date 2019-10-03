<?php
namespace Sinmax\AuditBundle\EventListener;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Sinmax\AuditBundle\Entity\Audit;
use Sinmax\AuditBundle\Entity\Change;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AuditService
{
		private $em;
		private $changes = [];

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
				$entityName = ClassUtils::getRealClass(get_class($entity));

				# We only audit the entities we are configured to audit
				// dump($this->container->getParameter('audit_classes')); exit;
				if (!in_array($entityName, $this->container->getParameter('audit_classes'))) return;

				if (count($args->getEntityChangeSet())<=0) return;
				$this->em = $args->getEntityManager();

				$key = $entity->getId()."|".str_replace($this->container->getParameter('audit_prefix'), '', $entityName);
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
