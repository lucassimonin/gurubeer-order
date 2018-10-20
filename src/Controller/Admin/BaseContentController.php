<?php

/**
 * Admin controller for manager a module
 *
 * @author Lucas Simonin <lsimonin2@gmail.com>
 */

namespace App\Controller\Admin;

use App\Form\Model\SearchContent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BaseContentController extends Controller
{
    /**
     * @param Request $request
     *
     * @return SearchContent
     */
    protected function initSearch(Request $request)
    {
        $filters = $request->query->get('search', array());
        $data = new SearchContent();
        $data->setName((isset($filters['name']))   ? $filters['name'] : '');

        return $data;
    }
}