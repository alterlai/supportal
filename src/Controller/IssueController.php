<?php

namespace App\Controller;

use App\Entity\DocumentDraft;
use App\Entity\Issue;
use App\Entity\User;
use App\Entity\UserAction;
use App\Form\DocumentDraftType;
use App\Form\IssueType;
use App\Repository\DraftStatusRepository;
use App\Repository\IssueRepository;
use App\Repository\UserActionRepository;
use App\Service\DocumentNameParserService;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IssueController extends AbstractController
{
    private $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    /**
     * List all open issues
     * @Route("/issues", name="issue.index")
     * @IsGranted("ROLE_USER")
     */
    public function index(IssueRepository $issueRepository)
    {
        /** @var User $user */
        $user = $this->getUser();
        $issues = $issueRepository->findBy(['issued_to' => $user->getId()]);
        return $this->render('issues/index.html.twig', ['issues' => $issues]);
    }


    /**
     * Show a specific issue with form. and handle an issue submission. When handling an issue submission, create a new draft and save the document.
     *
     * @Route("/issue/{id}", name="issue.show", methods={"GET", "POST"})
     * @param $id
     * @param IssueRepository $issueRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param DraftStatusRepository $draftStatusRepository
     * @param DocumentNameParserService $documentNameParserService
     * @return Response
     * @throws \Exception
     * @IsGranted("ROLE_USER")
     */
    public function show($id, IssueRepository $issueRepository, Request $request, EntityManagerInterface $entityManager, DraftStatusRepository $draftStatusRepository, DocumentNameParserService $documentNameParserService, UserActionRepository $userActionRepository)
    {
        $issue = $issueRepository->find($id);
        $form = $this->createForm(DocumentDraftType::class);
        $form->handleRequest($request);

        if ($issue)
        {
            if ($form->isSubmitted() && $form->isValid())
            {
                $data = $form->getData();
                $valid_file_extensions = $this->getParameter('app.allowed_file_extensions');
                $draftStatus = $draftStatusRepository->findOneBy(['name' => "In behandeling"]);

                if (! in_array($data['file_content']->getMimeType(), $valid_file_extensions))
                {
                    $this->addFlash("danger", "Incorrect bestandstype. Alleen DWG bestanden zijn toegestaan.");
                    return $this->redirectToRoute("issue.show", ['id' => $issue->getId()]);
                }

                $newFileName = $documentNameParserService->generateFileNameFromEntities(
                    $issue->getDocument()->getBuilding(),
                    $issue->getDocument()->getDiscipline(),
                    $issue->getDocument()->getDocumentType(),
                    $issue->getDocument()->getFloor(),
                    ($issue->getDocument()->getVersion()),
                    ".dwg"
                );
                $draft = new DocumentDraft();

                $draft->setDocument($issue->getDocument());
                $draft->setFileContent($data['file_content']);
                $draft->setUploadedAt(new \DateTime("now"));
                $draft->setUploadedBy($issue->getIssuedTo());
                $draft->setDraftStatus($draftStatus);

                $draft->setFileName("test.dwg");
                $entityManager->persist($draft);
                $entityManager->remove($issue);

                // Add submission to user action
                $userAction = $userActionRepository->findLastDWGByUserAndDocument($issue->getIssuedTo(), $issue->getDocument());
                $userAction->setReturnedAt(new \DateTime("now"));

                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Concept aangemaakt.'
                );

                /** @var User $user */
                $user = $this->getUser();
                $this->mailerService->sendUploadSuccessMail($user->getEmail(), $draft->getDocument()->getDocumentName());

                return $this->render("drafts/index.html.twig");
            }

            return $this->render("issues/show.html.twig", [
                "issue" => $issue,
                "action" => $this->generateUrl('issue.show', ["id" => $issue->getId()]),
                "form" => $form->createView(),
                "method" => "POST"
            ]);
        }
        else
        {
            return $this->render("errors/error.html.twig", ["message" => "Error finding issue."]);
        }
    }

    /**
     * Delete an issue.
     * @Route("/issue/{issueId}/delete", name="issue.delete")
     * @param int $issueId
     * @param IssueRepository $issueRepository
     * @param EntityManagerInterface $entityManager
     * @param UserActionRepository $userActionRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function delete(int $issueId, IssueRepository $issueRepository, EntityManagerInterface $entityManager, UserActionRepository $userActionRepository)
    {
        /** @var User $user */
        $user = $this->getUser();

        $issue = $issueRepository->findOneBy(['id' => $issueId, 'issued_to' => $user->getId()]);

        if (!$issue)
        {
            $this->addFlash("danger", "issue not found or does not belong to this user.");
            $this->redirectToRoute('issue.index');
        }

        // Set user action delivery date to today. User has reserved the document until this point
        $user_action = $userActionRepository->findLastDWGByUserAndDocument($user, $issue->getDocument());

        $user_action->setReturnedAt(new \DateTime("now"));

        $entityManager->remove($issue);

        $entityManager->flush();

        $this->addFlash("success", "Concept succesvol verwijderd.");

        return $this->redirectToRoute("issue.index");
    }

}

