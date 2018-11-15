<?php

/**
 * Admin controller for manager a module
 *
 * @author Lucas Simonin <lsimonin2@gmail.com>
 */

namespace App\Controller\Admin;

use App\Entity\Item;
use App\Entity\Order;
use App\Form\Model\SearchOrder;
use App\Form\Type\Order\OrderMediaType;
use App\Form\Type\Order\OrderType;
use App\Form\Type\Order\SearchOrderType;
use App\Security\OrderVoter;
use App\Services\Core\FileUploader;
use App\Services\Order\OrderService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class OrderController
 *
 * @package App\Controller\Admin
 */
class OrderController extends Controller
{
    /**
     * @param Request $request
     *
     * @return SearchOrder
     */
    protected function initSearch(Request $request)
    {
        $filters = $request->query->get('search', array());
        $data = new SearchOrder();
        $data->setName((isset($filters['name']))   ? $filters['name'] : '');

        return $data;
    }

    /**
     * @param Request     $request
     * @Route("/", name="admin_order_list")
     * @return Response
     */
    public function list(Request $request)
    {
        $data = $this->initSearch($request);
        $form = $this->createForm(SearchOrderType::class, $data);

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem('admin.order.list.title');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->getDoctrine()->getRepository(Order::class)->queryForSearch($data->getSearchData(), $this->getUser()),
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
     * @param OrderService $orderService
     * @Route("/order/create", name="admin_order_create")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function create(Request $request, OrderService $orderService)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem('admin.dashboard.label', $this->get("router")->generate("admin_order_list"));
        $breadcrumbs->addItem("admin.order.title.create");

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order->setCreator($this->getUser());
            $orderService->formatItems($order);
            $this->get('workflow.order')
                ->apply($order, Order::TRANSITION_WAIT_RETURN);
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->set(
                'notice',
                'admin.flash.created'
            );

            return $this->redirectToRoute('admin_order_edit', ['id' => $order->getId()]);
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
     * @param Order $order
     * @param OrderService $orderService
     * @Route("/order/edit/{id}", name="admin_order_edit")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function edit(Request $request, Order $order, OrderService $orderService)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem('admin.dashboard.label', $this->get("router")->generate("admin_order_list"));
        $breadcrumbs->addItem("admin.order.title.update");

        $originalItems = new ArrayCollection();
        /** @var Item $thematic */
        foreach ($order->getItems() as $item) {
            $originalItems->add($item);
        }

        $form = $this->createForm(OrderType::class, $order, [
            'create' => false
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($originalItems as $item) {
                if (false === $order->getItems()->contains($item)) {
                    $order->removeItem($item);
                    $orderService->remove($item);
                }
            }
            $orderService->save($order);
            $this->get('session')->getFlashBag()->set(
                'notice',
                'admin.flash.updated'
            );

            return $this->redirectToRoute('admin_order_list');
        }

        return $this->render(
            'admin/order/form.html.twig',
            array(
                'form' => $form->createView(),
                'type' => 'update',
                'order' => $order
            )
        );
    }

    /**
     * @param Order $order
     * @param OrderService $orderService
     * @Route("/order/delete/{id}", name="admin_order_delete")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Order $order, OrderService $orderService, FileUploader $fileUploader)
    {
        $this->denyAccessUnlessGranted(OrderVoter::ORDER_DELETE, $order);
        unlink($fileUploader->getTargetDirectory().'/'.$order->getPdf()->getFileName());
        $orderService->remove($order);
        $this->get('session')->getFlashBag()->set(
            'notice',
            'admin.flash.removed'
        );

        return $this->redirectToRoute('admin_order_list');
    }

    /**
     * @param $transition
     * @param Order $order
     * @Route("/order/{id}/apply-transition/{transition}", name="admin_order_apply_transition")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function applyTransition($transition, Order $order)
    {
        $manager = $this->getDoctrine()->getManager();
        $order->setBeforeState($order->getState());
        try {
            if (in_array($transition, Order::TRANSITION_AVAILABLE)) {
                $this->get('workflow.order')
                    ->apply($order, $transition);
                $manager->flush();
                /** @var Item $item */
                foreach ($order->getItems() as $item) {
                    if ($item->getQuantityUpdated() !== $item->getQuantity()) {
                        $item->setState(Item::STATE_UPDATED);
                    } else {
                        $item->setState(Item::STATE_NO_CHANGE);
                    }
                    $item->setQuantity($item->getQuantityUpdated());
                    $manager->persist($item);
                }
                $manager->flush();
            }
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
        }
        return $this->redirectToRoute('admin_order_list');
    }

    /**
     * @param Request $request
     * @param Order $order
     * @param OrderService $orderService
     * @Route("/order/pdf/{id}", name="admin_order_save_pdf")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function savePdf(Request $request, Order $order, OrderService $orderService)
    {
        $form = $this->createForm(OrderMediaType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $orderService->save($order);
            $this->get('session')->getFlashBag()->set(
                'notice',
                'admin.flash.updated'
            );
        }

        return $this->redirectToRoute('admin_order_list');
    }

    public function pdfForm(Order $order)
    {
        $form = $this->createForm(OrderMediaType::class, $order);

        return $this->render('admin/order/form_media.html.twig',
            [
                'form' => $form->createView(),
                'order_id' => $order->getId()
            ]);
    }
}
