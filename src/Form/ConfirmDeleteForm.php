<?php declare(strict_types = 1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

final class ConfirmDeleteForm extends AbstractType
{

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     * @param mixed[] $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('delete', SubmitType::class, [
                'label' => 'app.form.delete',
            ]);
    }

}
