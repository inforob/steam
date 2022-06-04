<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GameCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Game::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $imageField =  ImageField::new('image')
            ->setBasePath('uploads/games')
            ->setUploadDir('public/uploads/games')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(('new' == $pageName));

        return [
            TextField::new('title'),
            TextEditorField::new('description'),
            SlugField::new('slug')->setTargetFieldName('title'),
            AssociationField::new('category')->renderAsNativeWidget(),
            NumberField::new('price')->setNumDecimals(2),
            $imageField,
            ChoiceField::new('platform')->setChoices([
                'XBox' => 'xbox',
                'Pc' => 'pc',
                'Ios' => 'ios',
                'Android' => 'android',
            ]),
            BooleanField::new('published'),
        ];
    }
}
