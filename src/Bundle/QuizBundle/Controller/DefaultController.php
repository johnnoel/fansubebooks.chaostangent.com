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
        $seriesRepo = $this->get('fansubebooks.entity.repository.series');
        $series = $seriesRepo->findBy([], [ 'title' => 'ASC' ], 30);

        $midnightUtc = new \DateTime('now', new \DateTimezone('UTC'));
        $midnightUtc->setTime(0, 0, 0);

        $userRepo = $this->get('fansubebooks.quiz.entity.repository.user');
        $leaderboard = $userRepo->getLeaderboard(15, $midnightUtc);

        return [
            'user' => $this->getUser(),
            'series' => $series,
            'leaderboard' => $leaderboard,
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

        $questionRepo = $this->get('fansubebooks.quiz.entity.repository.question');
        $question = $questionRepo->getNextQuestion($user);

        return [
            'user' => $user,
            'question' => $question,
        ];
    }

    /**
     * @Route("/answer", name="quiz_answer", options={"expose": true})
     * @Method({"POST"})
     * @Template("ChaosTangentFansubEbooksQuizBundle:Default:question.html.twig")
     */
    public function answerAction(Request $request)
    {
        if (!$this->isCsrfTokenValid('quiz_answer', $request->request->get('token'))) {
            throw $this->createAccessDeniedException('Invalid token');
        }

        $seriesId = $request->request->get('answer');

        $user = $this->getUser();

        $questionRepo = $this->get('fansubebooks.quiz.entity.repository.question');
        $question = $questionRepo->getCurrentQuestion($user);

        if (!$question->isValidAnswer($seriesId)) {
            throw $this->createAccessDeniedException('Invalid answer');
        }

        $series = $question->getSeriesById($seriesId);

        $answerRepo = $this->get('fansubebooks.quiz.entity.repository.answer');
        $answer = $answerRepo->getCurrentAnswer($user);

        if ($answer === null) {
            // clever...
            throw $this->createAccessDeniedException('Race condition?');
        }

        $answer->setAnswer($series)
            ->setSkipped(false)
            ->setAnswered(new \DateTime('now'))
            ->setCorrect($question->isCorrect($answer));
        $answerRepo->update($answer);

        return [
            'user' => $user,
            'question' => $question,
            'answer' => $answer,
        ];
    }

    /**
     * @Route("/skip", name="quiz_skip", options={"expose": true})
     * @Method({"POST"})
     * @Template("ChaosTangentFansubEbooksQuizBundle:Default:question.html.twig")
     */
    public function skipAction(Request $request)
    {
        if (!$this->isCsrfTokenValid('quiz_skip', $request->request->get('token'))) {
            throw $this->createAccessDeniedException('Invalid token');
        }

        $user = $this->getUser();

        $questionRepo = $this->get('fansubebooks.quiz.entity.repository.question');
        $question = $questionRepo->getCurrentQuestion($user);

        $answerRepo = $this->get('fansubebooks.quiz.entity.repository.answer');
        $answer = $answerRepo->getCurrentAnswer($user);

        $answer->setSkipped(true)
            ->setAnswered(new \DateTime('now'));
        $answerRepo->update($answer);

        return [
            'user' => $user,
            'question' => $question,
            'answer' => $answer,
        ];
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
        $user = $this->getUser();

        if ($user !== null) {
            $userRepo = $this->get('fansubebooks.quiz.entity.repository.user');
            $userRepo->hydrateUserScores($user);
        }

        return [
            'user' => $this->getUser(),
        ];
    }
}
