<?php

namespace App\Form;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\EditorRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    private EditorRepository $editorRepository;
    private AuthorRepository $authorRepository;

    public function __construct(EditorRepository $editorRepository, AuthorRepository $authorRepository)
    {
        $this->editorRepository = $editorRepository;
        $this->authorRepository = $authorRepository;
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
            $dataEditor = $options['data']->getEditor()->getId();
        }

        $authors = [];

        foreach ($this->authorRepository->findAll() as $author) {
            if (!$name = $author->getCompletName()) {
                continue;
            }
            $authors[$name] = $author->getId();
        }

        if ($options['data']->getAuthor()) {
            $dataAuthor = $options['data']->getAuthor()->getId();
        }

        $builder
            ->add('title')
            ->add('slug')
            ->add('isbn')
            ->add('resume')
            ->add('price')
            ->add('author', ChoiceType::class, [
                'choices'  => $authors,
                'data' => $dataAuthor ?? null
            ])
            ->add('editor', ChoiceType::class, [
                'choices'  => $editors,
                'data' => $dataEditor ?? null
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
