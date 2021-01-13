<?php

namespace App\Form;

use App\Entity\Book;
use App\Repository\EditorRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    private EditorRepository $editorRepository;

    public function __construct(EditorRepository $editorRepository)
    {
        $this->editorRepository = $editorRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $editors = [
            'No editor' => null
        ];

        foreach ($this->editorRepository->findAll() as $editor) {
            $editors[$editor->getName()] = $editor->getId();
        }

        if ($options['data']->getEditor()) {
            $data = $options['data']->getEditor()->getId();
        }

        $builder
            ->add('title')
            ->add('slug')
            ->add('isbn')
            ->add('resume')
            ->add('price')
            ->add('editor', ChoiceType::class, [
                'choices'  => $editors,
                'data' => $data ?? null
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
