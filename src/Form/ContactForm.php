<?php declare(strict_types = 1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ContactForm extends AbstractType
{

    /**
     * @param mixed[] $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
                'label' => 'app.form.add',
            ]);
    }

}
