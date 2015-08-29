<?php

namespace ChaosTangent\FansubEbooks\Twitter;

/**
 * Twitter utility functions
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class Util
{
    /**
     * Get a Tweet ID from a full Twitter URL
     *
     * @param string $url
     * @return string|null
     */
    public static function getStatusIdFromUrl($url)
    {
        // just a status ID already?
        if (preg_match('/^\d+$/', $url) === 1) {
            return $url;
        }

        $matches = [];
        $matched = preg_match('#^https?://twitter.com/fansub_ebooks/status/(\d+)/?$#i', $url, $matches);
        if ($matched !== 1) {
            return null;
        }

        return $matches[1];
    }
}
