<?php

declare(strict_types=1);

namespace Maiscraft\Rbac\Contract;

/**
 * Guard 工厂接口
 * 驱动可以是字符串别名（extend 注册）或实现此接口的类名（容器解析）
 *
 * 字符串别名模式：
 *   AuthManager::extend('jwt', fn($config) => new JwtGuard($provider, $config))
 *   配置: ['driver' => 'jwt'] → 查找 extend 注册的闭包
 *
 * 类名模式：
 *   配置: ['driver' => JwtGuardFactory::class] → 容器解析后调用 make()
 */
interface GuardFactoryInterface
{
    /**
     * 根据 guard 配置创建 Guard 实例
     *
     * @param array $config guard 配置（来自 guards.{name}）
     * @param UserProviderInterface $provider 关联的 UserProvider
     */
    public function make(array $config, UserProviderInterface $provider): GuardInterface;
}