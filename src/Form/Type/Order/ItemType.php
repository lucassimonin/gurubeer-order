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
        $attrQuantity = in_array($options['state'], Order::STATE_NO_EDIT_QUANTITY) ? ['readonly' => true] : [];
        $quantityAttribute = $options['state'] === Order::STATE_WAIT_RETURN ? 'quantity' : 'quantityUpdated';
        $builder->add('name', TextType::class, [
            'label' => false,
            'attr' => $attrQuantity
        ]);
        $builder->add('type', ChoiceType::class, [
            'choices' => array_flip(Item::LIST_TYPE),
            'expanded' => false,
            'multiple' => false,
            'label' => false,
        ]);

        $builder->add($quantityAttribute, IntegerType::class, [
            'label' => false,
            'attr' => $attrQuantity
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
