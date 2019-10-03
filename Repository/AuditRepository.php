<?php
/**
 * Created by PhpStorm.
 * User: nicu
 * Date: 02.07.2018
 * Time: 16:07
 */

namespace TCR\AuditBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class AuditRepository extends EntityRepository
{
    /**
     * @param string $name
     * @param int $id
     * @return array|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAudit(string $name, int $id): ?array
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $queryStr = "SELECT
        (SELECT CONCAT_WS(' ', st_user.id, st_user.firstName, st_user.lastName) FROM st_user WHERE st_user.id = user ) as user,
                    created as date, field, value
                  FROM audit
                  LEFT JOIN audit_changes ON audit_changes.audit_id = audit.id
                  WHERE audit.entity= :name and audit.entityId = :id";

        $query = $connection->prepare($queryStr);
        $query->bindValue('name', $name);
        $query->bindValue('id', $id);
        $query->execute();
        $results = $query->fetchAll();

        return $results;
    }
}