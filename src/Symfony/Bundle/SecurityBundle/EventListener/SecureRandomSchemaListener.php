<?php

namespace Symfony\Bundle\SecurityBundle\EventListener;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Symfony\Component\Security\Core\Util\SecureRandomSchema;

class SecureRandomSchemaListener
{
    private $schema;
    private $registry;

    public function __construct(SecureRandomSchema $schema, ManagerRegistry $registry)
    {
        $this->schema = $schema;
        $this->registry = $registry;
    }

    public function postGenerateSchema(GenerateSchemaEventArgs $args)
    {
        if ($args->getEntityManager() !== $this->registry->getManager()) {
            return;
        }

        $this->schema->addToSchema($args->getSchema());
    }
}