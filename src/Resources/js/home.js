import LineList from 'linelist';

/**
 * Homepage
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
if (typeof window.__LINE_DATA__ != 'undefined' && window.__LINE_DATA__ !== null) {
    new LineList({
        element: document.getElementById('upcoming-grid'),
        lines: window.__LINE_DATA__,
        page: 1,
        pages: 1
    });
}
