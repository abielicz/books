<?php
/**
 * Tag type.
 */
namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Validator\Constraints as CustomAssert;

/**
 * Class TagType.
 *
 * @package Form
 */
class CommentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'matter',
            TextType::class,
            [
                'label' => 'label.comment',
                'required' => true,
                'attr' => [
                    'max_length' => 255,
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        ['groups' => ['comment-default']]
                    ),
                    new Assert\Length(
                        [
                            'groups' => ['comment-default'],
                            'min' => 3,
                            'max' => 255,
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => 'comment-default',
                'comment_repository' => null,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'comment_type';
    }
}