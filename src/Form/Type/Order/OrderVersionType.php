<?php

/**
 * Form type
 *
 * @author Lucas Simonin <lsimonin2@gmail.com>
 */

namespace App\Form\Type\Order;

use App\Entity\OrderVersion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *  Form Type
 */
class OrderVersionType extends AbstractType
{
    /**
     * Build Form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var OrderVersion $orderVersion */
        $orderVersion = $builder->getData();
        $builder->add('items', CollectionType::class, array(
            'entry_type' => ItemType::class,
            'entry_options' => array('label' => false, 'state' => $orderVersion->getState()),
            'allow_add' => true,
            'by_reference' => false,
            'allow_delete' => true,
            'label' => 'admin.order.form.items'
        ));
    }


    /**
     * Configure Options
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => OrderVersion::class,
            'translation_domain' => 'app',
            'before_state' => null
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['before_state'] = $options['before_state'];
    }
}
