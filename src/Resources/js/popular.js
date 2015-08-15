import LineList from 'linelist';

/**
 * Popular page
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */

new LineList({
    element: document.getElementById('popular'),
    lines: window.__LINE_DATA__,
    page: window.__PAGE__,
    pages: window.__PAGES__
});
