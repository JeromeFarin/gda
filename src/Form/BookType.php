<?php

namespace App\Form;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\CategoryRepository;
use App\Repository\EditorRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    private EditorRepository $editorRepository;
    private AuthorRepository $authorRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(EditorRepository $editorRepository, AuthorRepository $authorRepository, CategoryRepository $categoryRepository)
    {
        $this->editorRepository = $editorRepository;
        $this->authorRepository = $authorRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Book $book */
        $book = $options['data'];
        $editors = [
            'No editor' => null
        ];

        foreach ($this->editorRepository->findAll() as $editor) {
            $editors[$editor->getName()] = $editor->getId();
        }

        if ($book->getEditor()) {
            $dataEditor = $book->getEditor()->getId();
        }

        $authors = [];

        foreach ($this->authorRepository->findAll() as $author) {
            if (!$name = $author->getCompletName()) {
                continue;
            }
            $authors[$name] = $author->getId();
        }

        if ($book->getAuthor()) {
            $dataAuthor = $book->getAuthor()->getId();
        }

        $categories = [];

        foreach ($this->categoryRepository->findAll() as $category) {
            $categories[$category->getName()] = $category->getId();
        }

        $dataCategories = [];

        if ($book->getCategories()->toArray()) {
            foreach ($book->getCategories()->toArray() as $category) {
                $dataCategories[] = $category->getId();
            }
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
            ])
            ->add('categories', ChoiceType::class, [
                'multiple' => true,
                'choices'  => $categories,
                'data' => $dataCategories
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
