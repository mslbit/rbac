<?php

declare(strict_types=1);

namespace Maiscraft\Rbac\Contract;

/**
 * UserProvider 工厂接口
 * 驱动可以是字符串别名（extendProvider 注册）或实现此接口的类名（容器解析）
 *
 * 字符串别名模式：
 *   AuthManager::extendProvider('database', fn($config) => new DatabaseUserProvider($connection, $config))
 *   配置: ['driver' => 'database'] → 查找 extendProvider 注册的闭包
 *
 * 类名模式：
 *   配置: ['driver' => DatabaseUserProviderFactory::class] → 容器解析后调用 make()
 */
interface ProviderFactoryInterface
{
    /**
     * 根据 provider 配置创建 UserProvider 实例
     *
     * @param array $config provider 配置（来自 providers.{name}）
     */
    public function make(array $config): UserProviderInterface;
}