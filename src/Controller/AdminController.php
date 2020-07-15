<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\Organisation;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AdminController
 * Override default admincontroller functionality to encode user passwords
 * @package App\Controller
 */
class AdminController extends EasyAdminController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function persistUserEntity(User $user)
    {
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);
        $user->setSuspended(0);

        parent::persistEntity($user);
    }

    protected function updateUserEntity(User $user)
    {
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);

        parent::updateEntity($user);
    }

    protected function persistDocumentEntity(Document $document)
    {
        /** @var User $user */
        $user = $this->getUser();
        $document->setUploadedBy($user);

        parent::persistEntity($document);
    }

    protected function persistOrganisationEntity(Organisation $organisation)
    {
        if ($organisation->getLogoFileName() == null)
        {
            $organisation->setLogoFileName("");
        }
        $organisation->setUpdatedAt(new \DateTime('now'));

        parent::persistEntity($organisation);
    }


//    /**
//     * @param Document $document
//     * @throws \Exception
//     */
//    protected function persistDocumentEntity(Document $document)
//    {
//        throw new \Exception("test foutmet uploaden");
//    }

}

