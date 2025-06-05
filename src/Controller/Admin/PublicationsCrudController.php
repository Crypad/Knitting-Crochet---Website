<?php

namespace App\Controller\Admin;

use App\Entity\Type;
use App\Entity\Publications;
use Doctrine\DBAL\Types\JsonType;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PublicationsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Publications::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('images')
                ->setUploadDir('public/img/publications-images')
                ->setBasePath('/img/publications-images')
                ->setUploadedFileNamePattern('[uuid].[extension]')
                ->setFormTypeOptions([
                    'multiple' => true, // Allows multiple files in the form
                    'by_reference' => false, // Required for array storage
                ]),

            ArrayField::new('content')
                ->setHelp('Enter a list of values, one per line'),
            
            ArrayField::new('tags')
                ->setHelp('Enter a list of values, one per line'),

            AssociationField::new('ID_TYPE')
                ->setCrudController(TypeCrudController::class)
                ->setRequired(true),

            DateTimeField::new('created_at')
                ->setLabel('Created At')
                ->setDisabled() // Prevents editing
                ->hideOnForm()

        ];
    }
}
