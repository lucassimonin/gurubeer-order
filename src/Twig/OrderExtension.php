<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 2019-02-02
 * Time: 18:42
 */

namespace App\Twig;

use App\Entity\Order;
use App\Entity\OrderVersion;
use App\Services\Order\OrderVersionManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class OrderExtension extends AbstractExtension
{
    /**
     * @var OrderVersionManagerInterface
     */
    private $orderVersionManager;


    /**
     * OrderExtension constructor.
     * @param OrderVersionManagerInterface $orderVersionManager
     */
    public function __construct(OrderVersionManagerInterface $orderVersionManager)
    {
        $this->orderVersionManager = $orderVersionManager;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('last_version', [$this, 'getLastVersion']),
            new TwigFilter('before_version', [$this, 'getBeforeVersion']),
        ];
    }

    /**
     * @param Order $order
     * @return OrderVersion|null
     */
    public function getLastVersion(Order $order): ?OrderVersion
    {
        return $this->orderVersionManager->getLastVersion($order);
    }

    /**
     * @param OrderVersion $orderVersion
     * @return OrderVersion|null
     */
    public function getBeforeVersion(?OrderVersion $orderVersion): ?OrderVersion
    {
        if (null === $orderVersion) {
            return null;
        }

        return $this->orderVersionManager->getBeforeVersion($orderVersion);
    }
}
