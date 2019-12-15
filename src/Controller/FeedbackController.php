<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Entity\User;
use App\Form\FeedbackType;
use App\Services\Mailer\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FeedbackController extends AbstractController
{
    /** @var Mailer */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Route("/feedback", name="app.feedback")
     *
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index(Request $request)
    {
        $feedback = new Feedback();

        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $feedback->setAuthor($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($feedback);
            $em->flush();

            $this->addFlash('success', 'feedback.success');

            return $this->redirectToRoute('app.feedback');
        }

        return $this->render('feedback.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
