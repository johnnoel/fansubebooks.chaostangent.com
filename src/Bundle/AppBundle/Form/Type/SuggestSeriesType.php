<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Suggest series form
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class SuggestSeriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', [
            'required' => true,
            'trim' => true,
        ])->add('group', 'text', [
            'required' => true,
            'trim' => true,
        ])->add('suggest', 'submit');
    }

    public function getName()
    {
        return 'suggest_series';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'ChaosTangent\FansubEbooks\Entity\Series',
        ]);
    }
}
