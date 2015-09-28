<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;
use ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\Question,
    ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\Answer;

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
     * @Route("/logout", name="quiz_logout")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksQuizBundle:Default:index.html.twig")
     */
    public function indexAction()
    {
        return [
            'user' => $this->getUser(),
        ];
    }

    /**
     * @Route("/question", name="quiz_question")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksQuizBundle:Default:question.html.twig")
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
        if (!$this->isCsrfTokenValid('quiz_answer', $request->request->get('token'))) {
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

        $series = $question->getSeriesById($seriesId);

        $answerRepo = $om->getRepository(Answer::class);
        $answer = $answerRepo->getCurrentAnswer($user);

        if ($answer === null) {
            // clever...
            throw $this->createAccessDeniedException('Race condition?');
        }

        $answer->setAnswer($series)
            ->setSkipped(false)
            ->setAnswered(new \DateTime('now'));
        $answerRepo->update($answer);

        var_dump($answer->getAnswer()->getTitle(), $question->isCorrect($answer), $question->getCorrectSeries()->getTitle());

        return new Response('', 200, [ 'Content-type' => 'text/plain' ]);
    }

    /**
     * @Route("/skip", name="quiz_skip", options={"expose": true})
     * @Method({"POST"})
     */
    public function skipAction(Request $request)
    {
        if (!$this->isCsrfTokenValid('quiz_skip', $request->request->get('token'))) {
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
     * @Route("/leaderboard", name="quiz_leaderboard")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksQuizBundle:Default:leaderboard.html.twig")
     */
    public function leaderboardAction()
    {
        return [];
    }

    /**
     * @Template("ChaosTangentFansubEbooksQuizBundle:Default:user.html.twig")
     */
    public function userAction()
    {
        return [
            'user' => $this->getUser(),
        ];
    }
}
