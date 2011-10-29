<?php

namespace Symfony\Component\Security\Acl\Expression;

use Symfony\Component\Security\Acl\Permission\PermissionMapInterface;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\SecurityIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PermissionEvaluator
{
    private $aclProvider;
    private $oidRetrievalStrategy;
    private $sidRetrievalStrategy;
    private $permissionMap;
    private $allowIfObjectIdentityUnavailable;
    private $logger;

    public function __construct(AclProviderInterface $aclProvider,
        ObjectIdentityRetrievalStrategyInterface $oidRetrievalStrategy,
        SecurityIdentityRetrievalStrategyInterface $sidRetrievalStrategy,
        PermissionMapInterface $permissionMap,
        $allowIfObjectIdentityUnavailable = true,
        LoggerInterface $logger = null)
    {
        $this->aclProvider = $aclProvider;
        $this->oidRetrievalStrategy = $oidRetrievalStrategy;
        $this->sidRetrievalStrategy = $sidRetrievalStrategy;
        $this->permissionMap = $permissionMap;
        $this->logger = $logger;
    }

    public function hasPermission(TokenInterface $token, $object, $permission)
    {
        if (null === $masks = $this->permissionMap->getMasks($permission, $object)) {
            return false;
        }

        if (null === $object) {
            if (null !== $this->logger) {
                $this->logger->debug(sprintf('Object identity unavailable. Voting to %s', $this->allowIfObjectIdentityUnavailable? 'grant access' : 'abstain'));
            }

            return $this->allowIfObjectIdentityUnavailable ? true : false;
        } else if ($object instanceof FieldVote) {
            $field = $object->getField();
            $object = $object->getDomainObject();
        } else {
            $field = null;
        }

        if ($object instanceof ObjectIdentityInterface) {
            $oid = $object;
        } else if (null === $oid = $this->objectIdentityRetrievalStrategy->getObjectIdentity($object)) {
            if (null !== $this->logger) {
                $this->logger->debug(sprintf('Object identity unavailable. Voting to %s', $this->allowIfObjectIdentityUnavailable? 'grant access' : 'abstain'));
            }

            return $this->allowIfObjectIdentityUnavailable ? true : false;
        }

        $sids = $this->securityIdentityRetrievalStrategy->getSecurityIdentities($token);

        try {
            $acl = $this->aclProvider->findAcl($oid, $sids);

            if (null === $field && $acl->isGranted($masks, $sids, false)) {
                if (null !== $this->logger) {
                    $this->logger->debug('ACL found, permission granted. Voting to grant access');
                }

                return true;
            } else if (null !== $field && $acl->isFieldGranted($field, $masks, $sids, false)) {
                if (null !== $this->logger) {
                    $this->logger->debug('ACL found, permission granted. Voting to grant access');
                }

                return true;
            }

            if (null !== $this->logger) {
                $this->logger->debug('ACL found, insufficient permissions. Voting to deny access.');
            }

            return false;
        } catch (AclNotFoundException $noAcl) {
            if (null !== $this->logger) {
                $this->logger->debug('No ACL found for the object identity. Voting to deny access.');
            }

            return false;
        } catch (NoAceFoundException $noAce) {
            if (null !== $this->logger) {
                $this->logger->debug('ACL found, no ACE applicable. Voting to deny access.');
            }

            return false;
        }
    }
}