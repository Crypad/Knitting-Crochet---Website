<?php

namespace App\Controller\Admin;

use App\Entity\Annonce;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class AnnonceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Annonce::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextEditorField::new('content')
                ->setFormTypeOption('attr', [
                    // Au démarrage, 5 lignes, et pas de scroll vertical
                    'rows' => 5,
                    'style' => 'overflow:hidden;',
                    // Lorsqu'on tape, on ajuste la hauteur pour afficher tout le contenu
                    'oninput' => "this.style.height = ''; this.style.height = this.scrollHeight + 'px';"
                ])
        ];
    }
}