<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Suggest file/script form
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class SuggestFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', [
            'required' => true,
            'attr' => [
                'accept' => '.ass',
            ],
        ])->add('send', 'submit');
    }

    public function getName()
    {
        return 'suggest_file';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'ChaosTangent\FansubEbooks\Bundle\AppBundle\Form\Model\SuggestFile',
        ]);
    }
}
