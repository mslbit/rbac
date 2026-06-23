<?php

declare(strict_types=1);

namespace Maiscraft\Rbac\Contract;

/**
 * 菜单提供者接口
 * 框架适配层实现此接口，从数据库/配置读取菜单
 */
interface MenuProviderInterface
{
    /**
     * 获取所有启用的菜单
     * @return array<int, array{id: int, name: string, label: string, icon: ?string, path: ?string, parent_id: int, sort: int}>
     */
    public function getAllMenus(): array;

    /**
     * 获取角色关联的菜单ID
     * @param string[] $roles
     * @return int[]
     */
    public function getMenuIdsByRoles(array $roles): array;
}