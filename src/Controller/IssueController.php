<?php

namespace App\Controller;

use App\Entity\DocumentDraft;
use App\Entity\Issue;
use App\Form\DocumentDraftType;
use App\Form\IssueType;
use App\Repository\IssueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IssueController extends AbstractController
{
    /**
     * List all open issues
     * @Route("/issue", name="issue")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        return $this->render('issue/index.html.twig', [
            'controller_name' => 'IssueController',
        ]);
    }


    /**
     * Show a specific issue with form.
     *
     * @Route("/issue/{id}", name="issue", methods={"GET", "POST"})
     * @param $id
     * @param IssueRepository $issueRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @IsGranted("ROLE_USER")
     * @throws \Exception
     */
    public function show($id, IssueRepository $issueRepository, Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $issue = $issueRepository->find($id);
        $form = $this->createForm(DocumentDraftType::class);
        $form->handleRequest($request);

        if ($issue)
        {
            if ($form->isSubmitted() && $form->isValid())
            {
                return $this->handleSubmission($issue, $form, $entityManager, $logger);
            }

            return $this->render("pages/issues/show.html.twig", [
                "issue" => $issue,
                "action" => $this->generateUrl('issue', ["id" => $issue->getId()]),
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
     * Handle the form submission of an issue.
     * @param Issue $issue
     * @param FormInterface $form
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws \Exception
     */
    public function handleSubmission(Issue $issue, FormInterface $form, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $data = $form->getData();

        $draft = new DocumentDraft();

        $draft->setDocument($issue->getDocument());
        $draft->setFileName($issue->getDocument()->getFileName());
        $logger->info(implode("\n", $data));
        $draft->setFileContent($data['file_content']);
        $draft->setUploadedAt(new \DateTime("now"));
        $draft->setUploadedBy($issue->getIssuedTo());
//        $entityManager->persist($draft);
        $entityManager->remove($issue);
        $entityManager->flush();


        return $this->render("pages/blank.html.twig", ["message" => "Succesvol draft aangemaakt."]);
    }
}

