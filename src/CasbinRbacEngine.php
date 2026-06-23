<?php

declare(strict_types=1);

namespace Maiscraft\Rbac;

use Casbin\Enforcer;
use Casbin\Model\Model;
use Casbin\Persist\Adapter;
use Maiscraft\Rbac\Contract\MenuProviderInterface;
use Maiscraft\Rbac\Contract\RbacEngineInterface;

/**
 * Casbin 驱动的 RBAC 引擎
 * 核心逻辑零框架依赖，Adapter 和 MenuProvider 由适配包注入
 */
class CasbinRbacEngine implements RbacEngineInterface
{
    protected Enforcer $enforcer;
    protected MenuProviderInterface $menuProvider;

    public function __construct(Adapter $adapter, MenuProviderInterface $menuProvider)
    {
        $this->menuProvider = $menuProvider;

        $model = Model::newModelFromString(<<<RBAC
[request_definition]
r = sub, obj, act

[policy_definition]
p = sub, obj, act

[role_definition]
g = _, _

[policy_effect]
e = some(where (p.eft == allow))

[matchers]
m = g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act
RBAC);

        $this->enforcer = new Enforcer($model, $adapter);
        $this->enforcer->loadPolicy();
    }

    public function enforce(string $subject, string $object, string $action): bool
    {
        return $this->enforcer->enforce($subject, $object, $action);
    }

    public function getMenus(string $subject): array
    {
        $roles = $this->getRolesForUser($subject);
        if (empty($roles)) {
            return [];
        }

        $menuIds = $this->menuProvider->getMenuIdsByRoles($roles);
        if (empty($menuIds)) {
            return [];
        }

        $allMenus = $this->menuProvider->getAllMenus();
        $allowedMenus = array_filter($allMenus, fn(array $m) => in_array($m['id'], $menuIds, true));

        return MenuTreeBuilder::build($allowedMenus);
    }

    public function addPolicy(string $subject, string $object, string $action): void
    {
        $this->enforcer->addPolicy($subject, $object, $action);
    }

    public function removePolicy(string $subject, string $object, string $action): void
    {
        $this->enforcer->removePolicy($subject, $object, $action);
    }

    public function getRolesForUser(string $subject): array
    {
        return $this->enforcer->getRolesForUser($subject);
    }

    public function addRoleForUser(string $subject, string $role): bool
    {
        return $this->enforcer->addRoleForUser($subject, $role);
    }

    public function deleteRoleForUser(string $subject, string $role): bool
    {
        return $this->enforcer->deleteRoleForUser($subject, $role);
    }
}
