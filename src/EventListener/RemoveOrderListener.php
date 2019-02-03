<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 20/10/2018
 * Time: 08:50
 */

namespace App\EventListener;

use App\Event\OrderEvent;
use App\OrderEvents;
use App\Utils\FileUploaderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RemoveOrderListener implements EventSubscriberInterface
{
    /**
     * @var FileUploaderInterface
     */
    private $fileUploader;


    /**
     * RemoveOrderListener constructor.
     * @param FileUploaderInterface $fileUploader
     */
    public function __construct(FileUploaderInterface $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    public static function getSubscribedEvents()
    {
        return [
            OrderEvents::ORDER_DELETE => 'removePdf'
        ];
    }

    public function removePdf(OrderEvent $orderEvent)
    {
        $order = $orderEvent->getOrder();
        if (null !== $order->getFileName()) {
            unlink($this->fileUploader->getTargetDirectory().'/'.$order->getFileName());
        }
    }
}
