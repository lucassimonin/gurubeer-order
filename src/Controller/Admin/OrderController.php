<?php

/**
 * Admin controller for manager a module
 *
 * @author Lucas Simonin <lsimonin2@gmail.com>
 */

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Form\Model\SearchOrder;
use App\Form\Type\Order\OrderType;
use App\Form\Type\Order\SearchOrderType;
use App\Security\OrderVoter;
use App\Services\Order\OrderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class OrderController
 *
 * @package App\Controller\Admin
 * @Route("/orders")
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
     * @Route("/list", name="admin_order_list")
     * @return Response
     */
    public function list(Request $request)
    {
        $data = $this->initSearch($request);
        $form = $this->createForm(SearchOrderType::class, $data);

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem('admin.dashboard.label', $this->get("router")->generate("admin_dashboard"));
        $breadcrumbs->addItem('admin.order.list.title');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->getDoctrine()->getRepository(Order::class)->queryForSearch($data->getSearchData()),
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
     * @Route("/create", name="admin_order_create")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function create(Request $request, OrderService $orderService)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem('admin.dashboard.label', $this->get("router")->generate("admin_dashboard"));
        $breadcrumbs->addItem("admin.order.list.title", $this->get("router")->generate("admin_order_list"));
        $breadcrumbs->addItem("admin.order.title.create");

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $orderService->formatItems($order);
            $this->get('workflow.state')
                ->apply($order, Order::TRANSITION_IN_PROGRESS);
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
     * @Route("/edit/{id}", name="admin_order_edit")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function edit(Request $request, Order $order, OrderService $orderService)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem('admin.dashboard.label', $this->get("router")->generate("admin_dashboard"));
        $breadcrumbs->addItem("admin.order.list.title", $this->get("router")->generate("admin_order_list"));
        $breadcrumbs->addItem("admin.order.title.update");

        $form = $this->createForm(OrderType::class, $order, [
            'create' => false
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
                'type' => 'update'
            )
        );
    }

    /**
     * @param Order $order
     * @param OrderService $orderService
     * @Route("/delete/{id}", name="admin_order_delete")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Order $order, OrderService $orderService)
    {
        $this->denyAccessUnlessGranted(OrderVoter::ORDER_DELETE, $order);
        $orderService->remove($order);
        $this->get('session')->getFlashBag()->set(
            'notice',
            'admin.flash.removed'
        );

        return $this->redirectToRoute('admin_order_list');
    }
}
