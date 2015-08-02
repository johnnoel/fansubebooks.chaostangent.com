<?php

namespace ChaosTangent\FansubEbooks\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerAwareInterface,
    Psr\Log\LoggerAwareTrait;
use ChaosTangent\FansubEbooks\Event\SearchEvent,
    ChaosTangent\FansubEbooks\Event\SearchEvents;

/**
 * Logging listener
 *
 * That would make this a lumberjack then?
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class LoggingListener implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public static function getSubscribedEvents()
    {
        return [
            SearchEvents::SEARCH => 'onSearch',
            SearchEvents::SEARCH_SERIES => 'onSearch',
        ];
    }

    public function onSearch(SearchEvent $event)
    {
        if ($event->getSeries() !== null) {
            $message = sprintf('Search query "%s" on series "%s" [%d], page %d, took %f',
                $event->getQuery(),
                $event->getSeries()->getTitle(),
                $event->getSeries()->getId(),
                $event->getPage(),
                $event->getTime()
            );
        } else {
            $message = sprintf('Search query "%s", page %d, took %f',
                $event->getQuery(),
                $event->getPage(),
                $event->getTime()
            );
        }

        $this->logger->info($message);
    }
}
