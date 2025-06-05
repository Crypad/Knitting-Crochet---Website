<?php

namespace App\Controller\Admin;

use App\Entity\InstagramPub;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class InstagramPubCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return InstagramPub::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextEditorField::new('instagram_publications')
                ->setHelp('Les balises script seront automatiquement supprimées pour des raisons de sécurité.')
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof InstagramPub) {
            return;
        }

        // Nettoyer les <script> avant insertion et les <div>
        $scriptPattern = '/<script[^>]*>(.*?)<\/script>/s';
        $cleanedContent = preg_replace($scriptPattern, '', $entityInstance->getInstagramPublications());

        // Appliquer la modification
        $entityInstance->setInstagramPublications($cleanedContent);

        parent::persistEntity($entityManager, $entityInstance);
    }
}
