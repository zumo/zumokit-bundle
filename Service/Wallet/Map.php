<?php

/**
 * This file is part of the blockstar/zumokit-bundle package.
 *
 * (c) DLabs / Blockstar 2019
 * Author Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blockstar\ZumokitBundle\Service\Wallet;

/**
 * Class Map
 *
 * @package Blockstar\ZumokitBundle\Service\Wallet
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
class Map
{
    /**
     * @var \App\Repository\UserRepository
     */
    private $repository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Map constructor.
     *
     * @param \Blockstar\ZumokitBundle\Service\Wallet\App\Repository\UserRepository $repository
     * @param \Psr\Log\LoggerInterface                                              $logger
     */
    public function __construct(
        \App\Repository\UserRepository $repository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->logger     = $logger;
    }

    /**
     * @param array|null $item
     *
     * @return array|null
     */
    public function map(?array $item = []): ?array
    {
        if (
            !array_key_exists('walletAddress', $item) ||
            !array_key_exists('user', $item) ||
            !array_key_exists('email', $item['user'])
        ) {
            return null;
        }

        // todo save $item[''][''] to a variable to make code shorter...
        try {
            if (!method_exists($this->repository, 'findByEmail')) {
                $this->logger->critical(sprintf('Method findByEmail does not exist in %s ', get_class($this->repository)));
                $this->logger->critical('Trying findOneBy...');

                if (!method_exists($this->repository, 'findOneBy')) {
                    $this->logger->critical('Method findOneBy not found in repository!');
                    return null;
                }

                $user = $this->repository->findOneBy(['email' => $item['user']['email']]);
            } else {
                $user = $this->repository->findByEmail($item['user']['email']);
            }

            if (!$user instanceof \Blockstar\ZumokitBundle\Model\UserInterface) {
                $this->logger->critical(sprintf('Failed fetching user %s.', $item['user']['email']));
                return null;
            }
        } catch (\Exception $exception) {
            $this->logger->critical(
                sprintf('Failed fetching user %s: %s.', $item['user']['email'], $exception->getMessage())
            );
            return null;
        }

        try {
            return [
                'walletAddress' => $item['walletAddress'],
                'user'          => [
                    'id'       => (string) $user->getId(),
                    'email'    => $user->getEmail(),
                    'username' => $user->getEmail(),
                ],
            ];
        } catch (UniqueConstraintViolationException $e) {
            $this->logger->critical($e->getMessage());
            return null;
        }
    }
}
