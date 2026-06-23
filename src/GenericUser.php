<?php

declare(strict_types=1);

namespace Maiscraft\Rbac;

use Maiscraft\Rbac\Contract\AuthenticatableInterface;

/**
 * 通用用户实现
 * 当不使用 ORM 模型时，用此包装数据库行数据
 * 类似 Laravel 的 GenericUser
 */
class GenericUser implements AuthenticatableInterface
{
    protected string $authIdentifierName = 'id';
    protected string $authPasswordName = 'password';

    public function __construct(
        protected array $attributes = []
    ) {
    }

    public function getAuthIdentifierName(): string
    {
        return $this->authIdentifierName;
    }

    public function getAuthIdentifier(): int|string|null
    {
        return $this->attributes[$this->authIdentifierName] ?? null;
    }

    public function getAuthPasswordName(): string
    {
        return $this->authPasswordName;
    }

    public function getAuthPassword(): string
    {
        return $this->attributes[$this->authPasswordName] ?? '';
    }

    /**
     * 获取所有属性
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * 动态获取属性
     */
    public function __get(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * 动态设置属性
     */
    public function __set(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * 检查属性是否存在
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }
}