<?php declare(strict_types = 1);

namespace App\Form;

use Consistence\Type\ArrayType\ArrayType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContactForm extends AbstractType
{

    /**
     * @param mixed[] $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isUpdate = ArrayType::containsKey($options, 'is_update') && (bool) $options['is_update'];

        $builder
            ->add('firstname', TextType::class, [
                'label' => 'app.contact.firstname',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'app.contact.lastname',
            ])
            ->add('email', EmailType::class, [
                'label' => 'app.contact.email',
            ])
            ->add('phone', TextType::class, [
                'label' => 'app.contact.phone',
                'required' => false,
            ])
            ->add('notice', TextareaType::class, [
                'label' => 'app.contact.notice',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => $isUpdate ? 'app.form.edit' : 'app.form.add',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('is_update');
        $resolver->setAllowedTypes('is_update', 'bool');
    }

}
