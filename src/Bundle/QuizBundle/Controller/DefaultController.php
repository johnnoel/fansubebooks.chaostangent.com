<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ChaosTangent\FansubEbooks\Entity\Quiz\Question,
    ChaosTangent\FansubEbooks\Entity\Quiz\Answer;

/**
 * Quiz controller
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class DefaultController extends Controller
{
    /**
     * @Route("", name="quiz")
     * @Route("/login", name="quiz_login")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksQuizBundle:Default:index.html.twig")
     */
    public function indexAction()
    {
        $repo = $this->get('doctrine')->getManager()->getRepository('Quiz:User');
        var_dump(get_class($repo));
        return [];
    }

    /**
     * @Route("/question", name="quiz_question")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Quiz:question.html.twig")
     */
    public function questionAction(Request $request)
    {
        $user = $this->getUser();

        $questionRepo = $this->get('doctrine')->getManager()->getRepository(Question::class);
        $question = $questionRepo->getNextQuestion($user);

        return [
            'user' => $user,
            'question' => $question,
        ];
    }

    /**
     * @Route("/answer", name="quiz_answer", options={"expose": true})
     * @Method({"POST"})
     */
    public function answerAction(Request $request)
    {
        if ($this->isCsrfTokenValid('quiz_answer', $request->request->get('token'))) {
            throw $this->createAccessDeniedException('Invalid token');
        }

        $seriesId = $request->request->get('answer');

        $user = $this->getUser();
        $om = $this->get('doctrine')->getManager();

        $questionRepo = $om->getRepository(Question::class);
        $question = $questionRepo->getCurrentQuestion($user);

        if (!$question->isValidAnswer($seriesId)) {
            throw $this->createAccessDeniedException('Invalid answer');
        }

        $answerRepo = $om->getRepository(Answer::class);
        $answer = $answerRepo->getAnswer($question, $user);
        $answer->setAnswer($seriesId)
            ->setSkipped(false)
            ->setAnswered(new \DateTime('now'));

        $om->persist($answer);
        $om->flush();

        return [];
    }

    /**
     * @Route("/skip", name="quiz_skip", options={"expose": true})
     * @Method({"POST"})
     */
    public function skipAction(Request $request)
    {
        if ($this->isCsrfTokenValid('quiz_skip', $request->request->get('token'))) {
            throw $this->createAccessDeniedException('Invalid token');
        }

        $user = $this->getUser();
        $om = $this->get('doctrine')->getManager();

        $answerRepo = $om->getRepository(Answer::class);
        $answer = $answerRepo->getAnswer($question, $user);

        $answer->setSkipped(true)
            ->setAnswered(new \DateTime('now'));

        $om->persist($answer);
        $om->flush();

        return [];
    }

    /**
     * @Route("/scoreboard", name="quiz_scoreboard")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Quiz:scoreboard.html.twig")
     */
    public function scoreboardAction()
    {
        return [];
    }
}
