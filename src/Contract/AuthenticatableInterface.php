<?php

declare(strict_types=1);

namespace Maiscraft\Rbac\Contract;

/**
 * 可认证用户接口
 * 用户模型（Entity/DTO/GenericUser）必须实现此接口
 * Guard 通过此接口获取用户标识和密码，不关心具体实现
 */
interface AuthenticatableInterface
{
    /**
     * 获取唯一标识符字段名（如 'id'）
     */
    public function getAuthIdentifierName(): string;

    /**
     * 获取唯一标识符值（如 1）
     */
    public function getAuthIdentifier(): int|string|null;

    /**
     * 获取密码字段名（如 'password'）
     */
    public function getAuthPasswordName(): string;

    /**
     * 获取密码哈希值
     */
    public function getAuthPassword(): string;
}