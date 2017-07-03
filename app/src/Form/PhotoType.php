<?php
/**
 * Photo type.
 */

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PhotoType.
 *
 * @package Form
 */
class PhotoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'book_id',
            NumberType::class,
            [
                'label' => 'label.book_id',
                'required' => true,
                'attr' => [
                    'max_length' => 128,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ]
        );
        $builder->add(
            'photo',
            FileType::class,
            [
                'label' => 'label.photo',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Image(
                        [
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/png',
                                'image/jpeg',
                                'image/pjpeg',
                                'image/jpeg',
                                'image/pjpeg',
                            ],
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'photo_type';
    }
}