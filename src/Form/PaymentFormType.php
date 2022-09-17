<?php

namespace App\Form;

use App\Entity\UserOrder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'save',
                SubmitType::class,
                [
                'label' => 'Payer',
                'attr' => ['class' => 'btn btn-sm btn-dark'
                ]],
                
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserOrder::class,
        ]);
    }
}
