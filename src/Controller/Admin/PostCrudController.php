<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $imageField =  ImageField::new('image')
            ->setBasePath('uploads/posts')
            ->setUploadDir('public/uploads/posts')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(('new' == $pageName));


        return [
            TextField::new('title'),
            SlugField::new('slug')->setTargetFieldName('title'),
            AssociationField::new('topic')->renderAsNativeWidget(),
            TextEditorField::new('text'),
            $imageField,
            BooleanField::new('published'),
        ];
    }
}
