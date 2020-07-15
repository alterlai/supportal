<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\Organisation;
use App\Entity\User;
use App\Repository\BuildingRepository;
use App\Service\DocumentNameParserService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AdminController
 * Override default admincontroller functionality to encode user passwords
 * @package App\Controller
 */
class AdminController extends EasyAdminController
{
    private $passwordEncoder;
    private $buildingRepository;
    private $flashbag;
    private $documentNamer;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, BuildingRepository $buildingRepository, FlashBagInterface $flashbag, DocumentNameParserService $documentNamer)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->buildingRepository = $buildingRepository;
        $this->flashbag = $flashbag;
        $this->documentNamer = $documentNamer;
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
        // If the building is not owned by the parent location, generate an error. Document is niet aangemaakt.
        if (!$this->buildingRepository->findOneBy(['id' => $document->getBuilding(), 'location' => $document->getLocation()])) {
            $this->flashbag->add("danger", "Het gebouw is niet onderdeel van de geselecteerde locatie.");
            return $this->redirectToReferrer();
        }

        // Check if the file is somewhere on disk so we don't accidentally overwrite it.
        $fileName = $this->documentNamer->generateFileNameFromEntities($document->getBuilding(), $document->getDiscipline(), $document->getDocumentType(), $document->getVersion(), ".dwg", $document->getFloor());
        $fs = new Filesystem();
        if ($fs->exists($this->getParameter("document_upload_directory").$fileName))
        {
            $this->flashbag->add("danger", "Een document met deze bestandsnaam bestaat al. Het bestand is niet overschreven.");
            return $this->redirectToReferrer();
        }

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

