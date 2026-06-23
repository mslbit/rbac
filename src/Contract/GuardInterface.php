<?php

declare(strict_types=1);

namespace Maiscraft\Rbac\Contract;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Guard 接口（无状态）
 * 定义认证策略：如何从请求中识别用户
 *
 * 实现示例：
 * - JwtGuard：从 Authorization Header 解析 JWT
 * - TokenGuard：从 Query/Header 读取 API Token
 * - SessionGuard：从 Session 恢复用户
 */
interface GuardInterface
{
    /**
     * 从请求中认证用户
     * Guard 自行决定从 Header/Query/Attribute/Session 提取凭据
     * 通过注入的 UserProvider 获取用户实例
     */
    public function authenticate(ServerRequestInterface $request): static;

    /**
     * 判断当前用户是否已认证
     */
    public function check(): bool;

    /**
     * 判断当前用户是否为访客（未认证）
     */
    public function guest(): bool;

    /**
     * 获取当前认证用户
     */
    public function user(): ?AuthenticatableInterface;

    /**
     * 获取当前认证用户 ID
     */
    public function id(): int|string|null;

    /**
     * 获取关联的 UserProvider
     */
    public function getProvider(): UserProviderInterface;

    /**
     * 设置关联的 UserProvider
     */
    public function setProvider(UserProviderInterface $provider): static;
}