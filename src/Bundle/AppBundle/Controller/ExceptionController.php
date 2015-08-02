<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Controller;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseExceptionController;
use Symfony\Component\HttpKernel\Exception\FlattenException,
    Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;
use ChaosTangent\FansubEbooks\Entity\Repository\LineRepository;

/**
 * Exception controller
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class ExceptionController extends BaseExceptionController
{
    protected $lineRepo;

    public function __construct(LineRepository $lineRepo, \Twig_Environment $twig, $debug)
    {
        $this->lineRepo = $lineRepo;
        parent::__construct($twig, $debug);
    }

    /**
     * {@inheritDoc}
     * @see Symfony\Bundle\TwigBundle\Controller\ExceptionController::showAction
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        if ($exception->getStatusCode() != 404 || $request->getRequestFormat() != 'html') {
            return parent::showAction($request, $exception, $logger);
        }

        // 404 html
        $randomLine = $this->lineRepo->getRandom(1)[0];

        // from parent
        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
        $showException = $request->attributes->get('showException', $this->debug); // As opposed to an additional parameter, this maintains BC

        $code = $exception->getStatusCode();

        return new Response($this->twig->render(
            $this->findTemplate($request, $request->getRequestFormat(), $code, $showException), [
                'random_line' => $randomLine,
                'status_code' => $code,
                'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                'exception' => $exception,
                'logger' => $logger,
                'currentContent' => $currentContent,
            ]
        ));
    }
}
