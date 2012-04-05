<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\SecurityBundle\EventListener;

use Symfony\Component\Security\Acl\Dbal\Schema;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;

class AclSchemaListener
{
    private $schema;

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    public function postGenerateSchema(GenerateSchemaEventArgs $args)
    {
        $this->schema->addToSchema($args->getSchema());
    }
}
