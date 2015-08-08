import LinesAPI from './api';

/**
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */

const API = new LinesAPI();

export const VOTEUP_LINE = 'VOTEUP_LINE';
export const VOTEDOWN_LINE = 'VOTEDOWN_LINE';
export const FLAG_LINE = 'FLAG_LINE';

export const CHANGE_PAGE = 'CHANGE_PAGE';
export const CHANGE_PAGE_PENDING = 'CHANGE_PAGE_PENDING';
export const CHANGE_PAGE_FAILURE = 'CHANGE_PAGE_FAILURE';

export function voteUpLine(lineId) {
    return {
        type: VOTEUP_LINE,
        payload: lineId
    };
}

export function voteDownLine(lineId) {
    return {
        type: VOTEDOWN_LINE,
        payload: lineId
    };
}

export function flagLine(lineId) {
    return {
        type: FLAG_LINE,
        payload: lineId
    };
}

export function changePage(page) {
    return {
        types: [ CHANGE_PAGE_PENDING, CHANGE_PAGE, CHANGE_PAGE_FAILURE ],
        payload: API.getLines(page)
    };
}
