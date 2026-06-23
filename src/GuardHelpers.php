<?php

declare(strict_types=1);

namespace Maiscraft\Rbac;

use Maiscraft\Rbac\Contract\AuthenticatableInterface;
use Maiscraft\Rbac\Contract\UserProviderInterface;

/**
 * Guard 通用方法 trait
 * 提供 GuardInterface 的默认实现，减少重复代码
 * 类似 Laravel 的 GuardHelpers
 */
trait GuardHelpers
{
    protected ?AuthenticatableInterface $user = null;
    protected UserProviderInterface $provider;

    public function check(): bool
    {
        return !is_null($this->user());
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function user(): ?AuthenticatableInterface
    {
        return $this->user;
    }

    public function id(): int|string|null
    {
        return $this->user?->getAuthIdentifier();
    }

    public function getProvider(): UserProviderInterface
    {
        return $this->provider;
    }

    public function setProvider(UserProviderInterface $provider): static
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * 设置已认证用户（内部使用）
     */
    protected function setUser(?AuthenticatableInterface $user): static
    {
        $this->user = $user;
        return $this;
    }
}