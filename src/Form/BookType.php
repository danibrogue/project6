<?php
/*
namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('author')
            ->add('image')
            ->add('book_file')
            ->add('date_read')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
*/

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$builder
            ->add('name')
            ->add('author')
            ->add('image')
            ->add('bookFile')
            ->add('dateRead')
        ;*/

        $builder
            ->add('name', null, [
                'required' => true,
                'constraints' => [
                    new Length([
                        'maxMessage' => 'Максимальное число символов 255!',
                        'max' => 255,
                    ]),
                ],
                'label' => 'Name',
                'attr' => [
                    'class' => 'validate',
                ],
            ])
            ->add('author', null, [
                'required' => true,
                'constraints' => [
                    new Length([
                        'maxMessage' => 'Максимальное число символов 255!',
                        'max' => 255,
                    ]),
                ],
                'label' => 'Author',
                'attr' => [
                    'class' => 'validate',
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Book cover',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Данных тип файла не подерживается',
                        'maxSizeMessage' => 'Максимальный размер фото не более 5мб!',
                    ]),
                ],
            ])
            ->add('bookFile', FileType::class, [
                'label' => 'Book file',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/msword',
                            'text/xml',
                            'text/plain',
                            'text/markdown',
                            'application/vnd.oasis.opendocument.text',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ],
                        'mimeTypesMessage' => 'Данных тип файла не подерживается',
                        'maxSizeMessage' => 'Максимальный размер книги  не более 5 Мб!',
                    ]),
                ],
            ])
            ->add('dateRead', DateType::class, [
                'label' => 'Дата прочтения книги',
                'mapped' => true,
                'required' => true,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}