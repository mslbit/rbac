<?php

declare(strict_types=1);

namespace Maiscraft\Rbac\Contract;

/**
 * 用户提供者接口
 * 定义如何从持久化存储中获取和验证用户
 * Guard 不关心用户从哪来，只通过此接口获取
 *
 * 实现示例：
 * - DatabaseUserProvider：Query Builder 查表
 * - EloquentUserProvider：ORM 查模型
 */
interface UserProviderInterface
{
    /**
     * 通过唯一标识符获取用户
     */
    public function retrieveById(int|string $identifier): ?AuthenticatableInterface;

    /**
     * 通过凭据获取用户（如 username/email）
     * 不验证密码，只查找匹配的记录
     */
    public function retrieveByCredentials(array $credentials): ?AuthenticatableInterface;

    /**
     * 验证用户凭据（密码校验）
     */
    public function validateCredentials(AuthenticatableInterface $user, array $credentials): bool;
}