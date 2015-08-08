<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use ChaosTangent\FansubEbooks\Entity\Line,
    ChaosTangent\FansubEbooks\Entity\Vote,
    ChaosTangent\FansubEbooks\Entity\Flag;

/**
 * Lines controller
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @Route("", requirements={"id": "\d+"})
 */
class LinesController extends Controller
{
    /**
     * @Route("/l/{id}.{_format}", name="line",
     *      requirements={"id": "\d+", "_format": "|json"},
     *      defaults={"_format": "html"}
     * )
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Lines:index.html.twig")
     * @ParamConverter("line", class="Entity:Line", options={"repository_method": "getLine"})
     */
    public function indexAction(Line $line)
    {
        return [
            'line' => $line,
        ];
    }

    /**
     * @Route("/l/{id}/voteup.{_format}", name="line_voteup",
     *      requirements={"id": "\d+", "_format": "|json"},
     *      defaults={"_format": "html"},
     *      options={"expose": true}
     * )
     * @Method({"POST"})
     */
    public function voteUpAction(Line $line, Request $request)
    {
        $vote = new Vote();
        $vote->setLine($line)
            ->setIp($request->getClientIp())
            ->setPositive(true);

        $authChecker = $this->get('security.authorization_checker');
        if ($authChecker->isGranted('voteup', $vote) === false) {
            throw new AccessDeniedHttpException('Unable to vote from that IP address again');
        }

        $line->addVote($vote);

        $lineRepo = $this->get('fansubebooks.entity.repository.line_repository');
        $lineRepo->update($line);

        if ($request->getRequestFormat() == 'json') {
            $serializer = $this->get('jms_serializer');
            return new Response($serializer->serialize($vote, 'json'), 200, [
                'Content-Type' => 'application/json',
            ]);
        }

        // todo redirect to referrer
        return $this->redirect($this->generateUrl('line', [
            'id' => $line->getId(),
            'voted_up' => true,
        ]));
    }

    /**
     * @Route("/l/{id}/votedown.{_format}", name="line_votedown",
     *      requirements={"id": "\d+", "_format": "|json"},
     *      defaults={"_format": "html"},
     *      options={"expose": true}
     * )
     * @Method({"POST"})
     */
    public function voteDownAction(Line $line, Request $request)
    {
        $vote = new Vote();
        $vote->setLine($line)
            ->setIp($request->getClientIp())
            ->setPositive(false);

        $authChecker = $this->get('security.authorization_checker');
        if ($authChecker->isGranted('votedown', $vote) === false) {
            throw new AccessDeniedHttpException('Unable to vote from that IP address again');
        }

        $line->addVote($vote);

        $lineRepo = $this->get('fansubebooks.entity.repository.line_repository');
        $lineRepo->update($line);

        if ($request->getRequestFormat() == 'json') {
            $serializer = $this->get('jms_serializer');
            return new Response($serializer->serialize($vote, 'json'), 200, [
                'Content-Type' => 'application/json',
            ]);
        }

        // todo redirect to referrer
        return $this->redirect($this->generateUrl('line', [
            'id' => $line->getId(),
            'voted_down' => true,
        ]));
    }

    /**
     * @Route("/l/{id}/flag.{_format}", name="line_flag",
     *      requirements={"id": "\d+", "_format": "|json"},
     *      defaults={"_format": "html"},
     *      options={"expose": true}
     * )
     * @Method({"POST"})
     */
    public function flagAction(Line $line, Request $request)
    {
        $flag = new Flag();
        $flag->setLine($line)
            ->setIp($request->getClientIp());

        $authChecker = $this->get('security.authorization_checker');
        if ($authChecker->isGranted('flag', $flag) === false) {
            throw new AccessDeniedHttpException('Unable to flag from that IP address again');
        }

        $om = $this->get('doctrine')->getManager();
        $om->persist($flag);
        $om->flush();

        if ($request->getRequestFormat() == 'json') {
            $serializer = $this->get('jms_serializer');
            return new Response($serializer->serializer($flag, 'json'), 200, [
                'Content-Type' => 'application/json',
            ]);
        }

        return $this->redirect($this->generateUrl('line', [
            'id' => $line->getId(),
            'flagged' => true,
        ]));
    }

    /**
     * @Route("/random.{_format}", name="random",
     *      requirements={"_format": "|json"},
     *      defaults={"_format": "html"}
     * )
     * @Method({"GET"})
     */
    public function randomAction(Request $request)
    {
        $lineRepo = $this->get('doctrine')->getManager()->getRepository('Entity:Line');
        $random = $lineRepo->getRandom(1, false);
        $random = reset($random);

        if ($request->getRequestFormat() == 'json') {
            $serializer = $this->get('jms_serializer');
            return new Response($serializer->serialize($random, 'json'), 200, [
                'Content-Type' => 'application/json',
            ]);
        }

        return $this->redirect($this->generateUrl('line', [
            'id' => $random->getId()
        ]));
    }
}
