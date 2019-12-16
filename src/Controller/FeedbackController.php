<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Form\FeedbackType;
use App\Services\Mailer\MailerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FeedbackController extends AbstractController
{
    /** @var MailerInterface */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Route("/feedback", name="app.feedback", methods={"GET"})
     *
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function index(Request $request)
    {
        $feedback = new Feedback();

        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($feedback);
            $em->flush();

            // Send email here...

            $this->addFlash('success', 'feedback.success');

            return $this->redirectToRoute('app.feedback');
        }

        return $this->render('feedback/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
