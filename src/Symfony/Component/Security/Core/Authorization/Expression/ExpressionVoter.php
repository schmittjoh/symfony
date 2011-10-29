<?php

namespace Symfony\Component\Security\Core\Authorization\Expression;

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Expression-based voter.
 *
 * This voter allows to use complex access expression in a high-performance
 * way. This is the preferred voter for any non-simple access checks.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ExpressionVoter implements VoterInterface
{
    private $evaluators = array();
    private $compiler;
    private $cacheDir;
    private $expressionHandler;

    public function __construct(ExpressionHandlerInterface $expressionHandler) {
        $this->expressionHandler = $expressionHandler;
    }

    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function setCompiler(ExpressionCompiler $compiler)
    {
        $this->compiler = $compiler;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $result = VoterInterface::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$attribute instanceof Expression) {
                continue;
            }

            $result = VoterInterface::ACCESS_DENIED;
            if (!isset($this->evaluators[$attribute->expression])) {
                $this->evaluators[$attribute->expression] =
                    $this->createEvaluator($attribute);
            }

            if (call_user_func($this->evaluators[$attribute->expression],
                    $this->expressionHandler->createContext($token, $object))) {
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return $result;
    }

    public function supportsAttribute($attribute)
    {
        return $attribute instanceof Expression;
    }

    public function supportsClass($class)
    {
        return true;
    }

    protected function getCompiler()
    {
        if (null === $this->compiler) {
            throw new \RuntimeException('A compiler must be set.');
        }

        return $this->compiler;
    }

    private function createEvaluator(Expression $expr)
    {
        if ($this->cacheDir) {
            if (is_file($file = $this->cacheDir.'/'.sha1($expr->expression).'.php')) {
                return require $file;
            }

            $source = $this->getCompiler()->compileExpression($expr);
            file_put_contents($file, "<?php\n".$source);

            return require $file;
        }

        return eval($this->getCompiler()->compileExpression($expr));
    }
}