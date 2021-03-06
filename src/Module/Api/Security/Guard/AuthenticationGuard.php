<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Module\Api\Security\Guard;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Pandawa\Module\Api\Security\Authentication\AuthenticationManager;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class AuthenticationGuard implements Guard
{
    use GuardHelpers;

    /**
     * @var AuthenticationManager
     */
    private $authenticationManager;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $defaultAuthenticator;

    /**
     * Constructor.
     *
     * @param UserProvider          $userProvider
     * @param AuthenticationManager $authenticationManager
     * @param Request               $request
     * @param string                $defaultAuthenticator
     */
    public function __construct(UserProvider $userProvider, AuthenticationManager $authenticationManager, Request $request, string $defaultAuthenticator)
    {
        $this->provider = $userProvider;
        $this->authenticationManager = $authenticationManager;
        $this->request = $request;
        $this->defaultAuthenticator = $defaultAuthenticator;
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        if (null !== $this->user) {
            return $this->user;
        }

        if (null !== $preUser = $this->authenticationManager->verify($this->getAuthenticator(), $this->request)) {
            return $this->provider->retrieveById($preUser->getAuthIdentifier());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function id()
    {
        if ($this->user()) {
            return $this->user()->getAuthIdentifier();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $credentials = [])
    {
        if ($user = $this->provider->retrieveByCredentials($credentials)) {
            if ($this->provider->validateCredentials($user, $credentials)) {
                $this->user = $user;

                return true;
            }
        }

        return false;
    }

    private function getAuthenticator(): string
    {
        return $this->request->query('authenticator', $this->defaultAuthenticator);
    }
}
