<?php

declare(strict_types=1);

namespace Maiscraft\Rbac\Contract;

/**
 * RBAC 引擎接口
 * 权限检查 + 菜单解析
 */
interface RbacEngineInterface
{
    /**
     * 检查用户是否有权限
     */
    public function enforce(string $subject, string $object, string $action): bool;

    /**
     * 获取用户可见菜单
     * @return array<int, array{id: int, name: string, label: string, icon: ?string, path: ?string, parent_id: int, sort: int, children?: array}>
     */
    public function getMenus(string $subject): array;

    /**
     * 添加策略
     */
    public function addPolicy(string $subject, string $object, string $action): void;

    /**
     * 移除策略
     */
    public function removePolicy(string $subject, string $object, string $action): void;

    /**
     * 获取用户所有角色
     * @return string[]
     */
    public function getRolesForUser(string $subject): array;

    /**
     * 为用户添加角色
     */
    public function addRoleForUser(string $subject, string $role): bool;

    /**
     * 删除用户角色
     */
    public function deleteRoleForUser(string $subject, string $role): bool;
}