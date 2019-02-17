<?php

/**
 * Admin controller for manager a module
 *
 * @author Lucas Simonin <lsimonin2@gmail.com>
 */

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\OrderVersion;
use App\Form\Type\Order\OrderVersionType;
use App\Model\SearchOrder;
use App\Form\Type\Order\OrderMediaType;
use App\Form\Type\Order\OrderType;
use App\Form\Type\Order\SearchOrderType;
use App\Security\OrderVoter;
use App\Services\Order\ItemManagerInterface;
use App\Services\Order\OrderManagerInterface;
use App\Services\Order\OrderVersionManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrderController
 *
 * @package App\Controller\Admin
 */
class OrderController extends AbstractController
{
    /**
     * @var OrderManagerInterface
     */
    private $orderManager;
    /**
     * @var ItemManagerInterface
     */
    private $itemManager;
    /**
     * @var OrderVersionManagerInterface
     */
    private $orderVersionManager;

    /**
     * OrderController constructor.
     * @param OrderManagerInterface $orderManager
     * @param ItemManagerInterface $itemManager
     * @param OrderVersionManagerInterface $orderVersionManager
     */
    public function __construct(OrderManagerInterface $orderManager, ItemManagerInterface $itemManager, OrderVersionManagerInterface $orderVersionManager)
    {
        $this->orderManager = $orderManager;
        $this->itemManager = $itemManager;
        $this->orderVersionManager = $orderVersionManager;
    }


    /**
     * @param Request     $request
     * @Route("/", name="admin_order_list")
     * @return Response
     */
    public function list(Request $request): Response
    {
        $data = new SearchOrder($request->query->all());
        $form = $this->createForm(SearchOrderType::class, $data);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->getDoctrine()->getRepository(Order::class)->queryForSearch($data, $this->getUser()),
            $request->query->get('page', 1),
            20
        );

        return $this->render('admin/order/list.html.twig', array(
            'pagination' => $pagination,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request     $request
     * @Route("/order/create", name="admin_order_create")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function create(Request $request): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $orderVersion = $this->getOrderVersionManager()->createFirstOrderVersion($order, $this->getUser());
            $this->get('session')->getFlashBag()->set(
                'success',
                'admin.flash.created'
            );

            return $this->redirectToRoute('admin_order_edit', ['id' => $orderVersion->getId()]);
        }

        return $this->render(
            'admin/order/form.html.twig',
            array(
                'form' => $form->createView(),
                'type' => 'create'
            )
        );
    }

    /**
     * @param Request $request
     * @param OrderVersion $orderVersion
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/order/edit/{id}", name="admin_order_edit")
     */
    public function edit(Request $request, OrderVersion $orderVersion): Response
    {
        $this->getItemManager()->setOriginalItems($orderVersion);
        $form = $this->createForm(OrderVersionType::class, $orderVersion, [
            'before_state' => $this->getOrderVersionManager()->getBeforeState($orderVersion)
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $orderVersionNew = $this->getItemManager()->removeOldItems($this->getOrderVersionManager()->createOrderVersion($orderVersion, $this->getUser()));
            $this->getItemManager()->addOriginalItems($orderVersion);
            $this->getOrderVersionManager()->save($orderVersion);
            $this->getOrderVersionManager()->save($orderVersionNew);
            $this->get('session')->getFlashBag()->set(
                'success',
                'admin.flash.updated'
            );

            return $this->redirectToRoute('admin_order_list');
        }

        return $this->render(
            'admin/order/form.html.twig',
            array(
                'form' => $form->createView(),
                'type' => 'update',
                'order' => $orderVersion
            )
        );
    }

    /**
     * @param Order $order
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/order/delete/{id}", name="admin_order_delete")
     */
    public function delete(Order $order): Response
    {
        $this->denyAccessUnlessGranted(OrderVoter::ORDER_DELETE, $order);
        $this->getOrderManager()->remove($order);
        $this->get('session')->getFlashBag()->set(
            'warning',
            'admin.flash.removed'
        );

        return $this->redirectToRoute('admin_order_list');
    }

    /**
     * @param $transition
     * @param OrderVersion $orderVersion
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/order/{id}/apply-transition/{transition}", name="admin_order_apply_transition")
     */
    public function applyTransition(OrderVersion $orderVersion, $transition): Response
    {
        try {
            $this->getOrderVersionManager()->apply($transition, $orderVersion, $this->getUser());
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
        }
        return $this->redirectToRoute('admin_order_list');
    }

    /**
     * @param Request $request
     * @param Order $order
     * @Route("/order/pdf/{id}", name="admin_order_save_pdf")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function savePdf(Request $request, Order $order): Response
    {
        $form = $this->createForm(OrderMediaType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getOrderManager()->save($order);
            $this->get('session')->getFlashBag()->set(
                'success',
                'admin.flash.updated'
            );
        }

        return $this->redirectToRoute('admin_order_list');
    }

    public function pdfForm(Order $order): Response
    {
        $form = $this->createForm(OrderMediaType::class, $order);

        return $this->render(
            'admin/order/form_media.html.twig',
            [
                'form' => $form->createView(),
                'order_id' => $order->getId()
            ]
        );
    }

    /**
     * @return OrderManagerInterface
     */
    public function getOrderManager(): OrderManagerInterface
    {
        return $this->orderManager;
    }

    /**
     * @return ItemManagerInterface
     */
    public function getItemManager(): ItemManagerInterface
    {
        return $this->itemManager;
    }

    /**
     * @return OrderVersionManagerInterface
     */
    public function getOrderVersionManager(): OrderVersionManagerInterface
    {
        return $this->orderVersionManager;
    }

    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            'knp_paginator' => '?knp_paginator',
        ]);
    }
}
