<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use ChaosTangent\FansubEbooks\Bundle\AppBundle\Form\Type\SuggestFileType,
    ChaosTangent\FansubEbooks\Bundle\AppBundle\Form\Type\SuggestSeriesType;
use ChaosTangent\FansubEbooks\Entity\Suggestion;

/**
 * Help controller
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @Route("/help")
 */
class HelpController extends Controller
{
    /**
     * @Route("", name="help")
     * @Method({"GET"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Help:index.html.twig")
     */
    public function helpAction()
    {
        $suggestFile = $this->createForm(new SuggestFileType(), null, [
            'action' => $this->generateUrl('help_suggestfile'),
        ]);
        $suggestSeries = $this->createForm(new SuggestSeriesType(), null, [
            'action' => $this->generateUrl('help_suggestseries'),
        ]);

        return [
            'suggest_file' => $suggestFile->createView(),
            'suggest_series' => $suggestSeries->createView(),
        ];
    }

    /**
     * @Route("/suggest-series", name="help_suggestseries")
     * @Method({"POST"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Help:suggest-series.html.twig")
     */
    public function suggestSeries(Request $request)
    {
        $form = $this->createForm(new SuggestSeriesType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $suggestSeries = $form->getData();

            $suggestion = new Suggestion();
            $suggestion->setIp($request->getClientIp())
                ->setType('series')
                ->setData($suggestSeries);

            $om = $this->get('doctrine')->getManager();
            $om->persist($suggestion);
            $om->flush();

            return [];
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/suggest-file", name="help_suggestfile")
     * @Method({"POST"})
     * @Template("ChaosTangentFansubEbooksAppBundle:Help:suggest-file.html.twig")
     */
    public function suggestFile(Request $request)
    {
        $form = $this->createForm(new SuggestFileType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            // todo try/catch
            $suggestFile = $form->getData();
            // set the originally uploaded name
            $suggestFile->uploadedFilename = $suggestFile->file->getClientOriginalName();

            // generate a unique filename
            $filename = (new \DateTime())->format('U').'-'.hash_file('md5', $suggestFile->file->getPathname());
            $dir = $this->container->getParameter('script_temp_storage');
            // move it and replace the existing file
            $suggestFile->file = $suggestFile->file->move($dir, $filename);

            $suggestion = new Suggestion();
            $suggestion->setIp($request->getClientIp())
                ->setType('script')
                ->setData($suggestFile);

            $om = $this->get('doctrine')->getManager();
            $om->persist($suggestion);
            $om->flush();

            return [];
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
