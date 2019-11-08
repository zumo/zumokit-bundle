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

use Blockstar\ZumokitBundle\Model\WalletInterface;

/**
 * Class Sync
 *
 * @package Blockstar\ZumokitBundle\Service\Wallet
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
class Sync
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
     * @param \App\Repository\UserRepository $repository
     * @param \Psr\Log\LoggerInterface       $logger
     */
    public function __construct(
        \App\Repository\UserRepository $repository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->logger     = $logger;
    }

    /**
     * @param string|null $walletAddress
     * @param string|null $userId
     *
     * @return void
     */
    public function sync(?string $walletAddress, ?string $userId): void
    {
        try {
            $user = $this->repository->findOneBy(['id' => $userId]);
        } catch (\Exception $exception) {
            $this->logger->critical(sprintf('Failed fetching user %s: %s', $userId, $exception->getMessage()));
            return;
        }

        if (!method_exists($this->repository, 'findEntityBy')) {
            $this->logger->critical(sprintf('Method findEntityBy does not exist in %s ', get_class($this->repository)));
            $this->logger->critical('Trying findBy...');
        } else {
            if ($this->repository->findEntityBy('App\Entity\Wallet', ['address' => $walletAddress])) {
                return;
            }
        }

        try {
            if (!($user->getWallet() instanceof WalletInterface)) {
                $localWallet = new \App\Entity\Wallet($walletAddress);
                $user->setWallet($localWallet);
                $this->repository->save($localWallet);
            }
        } catch (UniqueConstraintViolationException $exception) {
            $this->logger->critical(sprintf('A violation occured: %s ', $exception->getMessage()));
            return;
        }
    }
}
