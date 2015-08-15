/**
 * animated scroll to element without jQuery
 *
 * @see http://stackoverflow.com/a/8918062/4545769
 */

function scrollTo(element, to, duration) {
    if (duration < 0) {
        return;
    }

    let diff = to - element.scrollTop,
        perTick = diff / duration * 10;

    setTimeout(() => {
        element.scrollTop = element.scrollTop + perTick;
        if (element.scrollTop == to) {
            return;
        }

        scrollTo(element, to, duration - 10);
    }, 10);
}

export default scrollTo;
