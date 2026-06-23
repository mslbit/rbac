<?php

declare(strict_types=1);

namespace Maiscraft\Rbac;

/**
 * 菜单树构建器
 * 将扁平菜单列表构建为树形结构
 * 从 CasbinRbacEngine 提取为独立工具类
 */
class MenuTreeBuilder
{
    /**
     * 构建菜单树
     *
     * @param array<int, array{id: int, name: string, label: string, icon: ?string, path: ?string, parent_id: int, sort: int}> $menus
     * @return array<int, array{id: int, name: string, label: string, icon: ?string, path: ?string, parent_id: int, sort: int, children?: array}>
     */
    public static function build(array $menus): array
    {
        $indexed = [];
        foreach ($menus as $menu) {
            $indexed[$menu['id']] = array_merge($menu, ['children' => []]);
        }

        $tree = [];
        foreach ($indexed as $menu) {
            if ($menu['parent_id'] > 0 && isset($indexed[$menu['parent_id']])) {
                $indexed[$menu['parent_id']]['children'][] = &$indexed[$menu['id']];
            } else {
                $tree[] = &$indexed[$menu['id']];
            }
        }

        usort($tree, fn($a, $b) => $a['sort'] <=> $b['sort']);

        return $tree;
    }
}