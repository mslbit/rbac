<?php

declare(strict_types=1);

namespace Maiscraft\Rbac\Contract;

/**
 * 有状态 Guard 接口
 * 扩展 GuardInterface，增加登录/登出能力
 *
 * 适用于 SessionGuard 等需要主动写入认证状态的场景
 * JwtGuard 等无状态 Guard 只需实现 GuardInterface
 */
interface StatefulGuardInterface extends GuardInterface
{
    /**
     * 尝试用凭据登录
     * 返回是否成功
     */
    public function attempt(array $credentials): bool;

    /**
     * 登录指定用户
     */
    public function login(AuthenticatableInterface $user): void;

    /**
     * 通过 ID 登录
     */
    public function loginUsingId(int|string $id): ?AuthenticatableInterface;

    /**
     * 登出当前用户
     */
    public function logout(): void;
}