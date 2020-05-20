<?php

namespace App\Controller;

use App\Form\IssueType;
use App\Repository\IssueRepository;
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
     * @Route("/issue/{id}", name="issue.show", methods={"GET"})
     * @param $id
     * @param IssueRepository $issueRepository
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function show($id, IssueRepository $issueRepository)
    {
        $issue = $issueRepository->find($id);

        if ($issue)
        {
            $form = $this->createForm(IssueType::class);
            return $this->render("pages/issues/show.html.twig", [
                "issue" => $issue,
                "action" => $this->generateUrl('issue.handle', ["id" => $issue->getId()]),
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
     *
     * @Route("/issue/{id}", name="issue.handle", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request)
    {
        //TODO
        return $this->render("pages/blank.html.twig");
    }
}



//$document = new Document();
//
//$form = $this->createForm(DocumentType::class, $document);
//
//$form->handleRequest($request);
//if ($form->isSubmitted() && $form->isValid()) {
//    $data = $form->getData();
//    $entityManager->persist($data);
//    $entityManager->flush();
//
//    return $this->render("pages/upload_success.html.twig");
//}
//
//return $this->render('pages/upload.html.twig', [
//    'form' => $form->createView(),
//    'action' => $this->generateUrl('upload'),
//    'method' => 'POST'
//]);
