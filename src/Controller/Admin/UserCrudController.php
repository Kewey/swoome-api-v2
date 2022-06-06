<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{

    private $adminUrlGenerator;
    private $entityManager;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $entityManager)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureActions(Actions $actions): Actions
    {
        $changePassword = Action::new('changePassword', 'Modifier le mot de passe', 'fa fa-key')->addCssClass('btn btn-secondary')
            ->linkToRoute('changePassword', function (User $user): array {
                return [
                    'userid' => $user->getId(),
                ];
            });

        return $actions
            ->add(Crud::PAGE_DETAIL, $changePassword)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, $changePassword);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('username'),
            EmailField::new('email'),
            ChoiceField::new('Roles', 'Roles')->setChoices([
                'Administrateur' => 'ROLE_ADMIN',
                'Utilisateur' => 'ROLE_USER'
            ])->setFormTypeOptions([
                'required' => true,
                'multiple' => true,
                'expanded' => false,
            ]),
            AssociationField::new('groups', 'Groupes'),
            BooleanField::new('isVerified', 'Compte vérifié')
        ];
    }

    /**
     * @Route("/reset/{userid}", name="changePassword")
     */
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, string $userid): Response
    {
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        $user =  $this->entityManager->getRepository(User::class)->findOneBy(['id' => $userid]);
        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($hashedPassword);
            $this->entityManager->flush();


            $url = $this->adminUrlGenerator
                ->setController(UserCrudController::class)
                ->generateUrl();

            return $this->redirect($url);
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }
}
