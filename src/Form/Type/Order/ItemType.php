<?php

/**
 * Form type
 *
 * @author Lucas Simonin <lsimonin2@gmail.com>
 */

namespace App\Form\Type\Order;

use App\Entity\Item;
use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 *  Form Type
 */
class ItemType extends AbstractType
{
    /**
     * Build Form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => false,
            'attr' => in_array($options['state'], Order::STATE_NO_EDIT_QUANTITY) ? ['readonly' => true] : []
        ]);
        $builder->add('type', ChoiceType::class, [
            'choices' => array_flip(Item::LIST_TYPE),
            'expanded' => false,
            'multiple' => false,
            'label' => false,
        ]);
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            array($this, 'onPreSetData')
        );
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var Item $item */
        $item = $event->getData();
        $state = $form->getConfig()->getOption('state');
        $quantityAttribute = $state === Order::STATE_DRAFT ? 'quantity' : 'quantityUpdated';
        $form->add($quantityAttribute, IntegerType::class, [
            'label' => false,
            'attr' => in_array($state, Order::STATE_NO_EDIT_QUANTITY) ? ['readonly' => true] : [],
            'data' => $item === null ? Item::DEFAULT_QUANTITY : $item->getQuantityUpdated()
        ]);
    }

    /**
     * Configure Options
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Item::class,
            'translation_domain' => 'app',
            'state' => Order::STATE_DRAFT
        ));
    }
}
