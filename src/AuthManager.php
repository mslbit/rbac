<?php

declare(strict_types=1);

namespace Maiscraft\Rbac;

use Maiscraft\Rbac\Contract\GuardFactoryInterface;
use Maiscraft\Rbac\Contract\GuardInterface;
use Maiscraft\Rbac\Contract\ProviderFactoryInterface;
use Maiscraft\Rbac\Contract\UserProviderInterface;
use Psr\Container\ContainerInterface;

/**
 * Auth 管理器
 * 管理多 Guard + 多 Provider，懒加载 + 缓存
 * 参考 Laravel AuthManager，适配 PSR-11 容器
 *
 * Driver 解析策略：
 * 1. 字符串别名 → 查找 extend/extendProvider 注册的闭包
 * 2. 类名（实现 FactoryInterface）→ 容器解析后调用 make()
 */
class AuthManager
{
    /**
     * 已解析的 Guard 实例缓存
     * @var array<string, GuardInterface>
     */
    protected array $guards = [];

    /**
     * 已解析的 Provider 实例缓存
     * @var array<string, UserProviderInterface>
     */
    protected array $providers = [];

    /**
     * 自定义 Guard 驱动创建器（字符串别名 → 闭包）
     * @var array<string, callable(ContainerInterface, array, UserProviderInterface): GuardInterface>
     */
    protected array $guardCreators = [];

    /**
     * 自定义 Provider 驱动创建器（字符串别名 → 闭包）
     * @var array<string, callable(ContainerInterface, array): UserProviderInterface>
     */
    protected array $providerCreators = [];

    public function __construct(
        protected ContainerInterface $container,
        protected array $config
    ) {
    }

    /**
     * 获取 Guard 实例（懒加载 + 缓存）
     */
    public function guard(?string $name = null): GuardInterface
    {
        $name = $name ?? $this->getDefaultGuard();

        return $this->guards[$name] ??= $this->resolveGuard($name);
    }

    /**
     * 获取 UserProvider 实例（懒加载 + 缓存）
     */
    public function provider(?string $name = null): UserProviderInterface
    {
        $name = $name ?? $this->getDefaultProvider();

        return $this->providers[$name] ??= $this->resolveProvider($name);
    }

    /**
     * 注册自定义 Guard 驱动
     */
    public function extend(string $driver, callable $callback): static
    {
        $this->guardCreators[$driver] = $callback;
        return $this;
    }

    /**
     * 注册自定义 Provider 驱动
     */
    public function extendProvider(string $driver, callable $callback): static
    {
        $this->providerCreators[$driver] = $callback;
        return $this;
    }

    /**
     * 代理到默认 Guard
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->guard()->{$method}(...$parameters);
    }

    /**
     * 获取默认 Guard 名称
     */
    public function getDefaultGuard(): string
    {
        return $this->config['default']['guard'] ?? 'jwt';
    }

    /**
     * 获取默认 Provider 名称
     */
    public function getDefaultProvider(): string
    {
        return $this->config['default']['provider'] ?? 'users';
    }

    /**
     * 获取 Guard 配置
     */
    public function getGuardConfig(string $name): ?array
    {
        return $this->config['guards'][$name] ?? null;
    }

    /**
     * 获取 Provider 配置
     */
    public function getProviderConfig(string $name): ?array
    {
        return $this->config['providers'][$name] ?? null;
    }

    /**
     * 解析 Guard 实例
     */
    protected function resolveGuard(string $name): GuardInterface
    {
        $config = $this->getGuardConfig($name);
        if ($config === null) {
            throw new \InvalidArgumentException("Auth guard [{$name}] is not defined.");
        }

        $driver = $config['driver'] ?? null;
        if ($driver === null) {
            throw new \InvalidArgumentException("Auth guard [{$name}] has no driver configured.");
        }

        // 1. 字符串别名 → 查找 extend 注册的闭包
        if (isset($this->guardCreators[$driver])) {
            $provider = $this->provider($config['provider'] ?? null);
            return $this->guardCreators[$driver]($this->container, $config, $provider);
        }

        // 2. 类名（实现 GuardFactoryInterface）→ 容器解析后调用 make()
        if (class_exists($driver) && is_a($driver, GuardFactoryInterface::class, true)) {
            $factory = $this->container->get($driver);
            $provider = $this->provider($config['provider'] ?? null);
            return $factory->make($config, $provider);
        }

        throw new \InvalidArgumentException("Auth guard driver [{$driver}] is not supported. Use extend() or implement GuardFactoryInterface.");
    }

    /**
     * 解析 Provider 实例
     */
    protected function resolveProvider(string $name): UserProviderInterface
    {
        $config = $this->getProviderConfig($name);
        if ($config === null) {
            throw new \InvalidArgumentException("Auth provider [{$name}] is not defined.");
        }

        $driver = $config['driver'] ?? null;
        if ($driver === null) {
            throw new \InvalidArgumentException("Auth provider [{$name}] has no driver configured.");
        }

        // 1. 字符串别名 → 查找 extendProvider 注册的闭包
        if (isset($this->providerCreators[$driver])) {
            return $this->providerCreators[$driver]($this->container, $config);
        }

        // 2. 类名（实现 ProviderFactoryInterface）→ 容器解析后调用 make()
        if (class_exists($driver) && is_a($driver, ProviderFactoryInterface::class, true)) {
            $factory = $this->container->get($driver);
            return $factory->make($config);
        }

        throw new \InvalidArgumentException("Auth provider driver [{$driver}] is not supported. Use extendProvider() or implement ProviderFactoryInterface.");
    }
}