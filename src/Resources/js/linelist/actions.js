import LinesAPI from './api';

/**
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */

export const VOTEUP_LINE = 'VOTEUP_LINE';
export const VOTEUP_LINE_PENDING = 'VOTEUP_LINE_PENDING';
export const VOTEUP_LINE_FAILURE = 'VOTEUP_LINE_FAILURE';

export const VOTEDOWN_LINE = 'VOTEDOWN_LINE';
export const VOTEDOWN_LINE_PENDING = 'VOTEDOWN_LINE_PENDING';
export const VOTEDOWN_LINE_FAILURE = 'VOTEDOWN_LINE_FAILURE';

export const FLAG_LINE = 'FLAG_LINE';
export const FLAG_LINE_PENDING = 'FLAG_LINE_PENDING';
export const FLAG_LINE_FAILURE = 'FLAG_LINE_FAILURE';

export const CHANGE_PAGE = 'CHANGE_PAGE';
export const CHANGE_PAGE_PENDING = 'CHANGE_PAGE_PENDING';
export const CHANGE_PAGE_FAILURE = 'CHANGE_PAGE_FAILURE';

export function voteUpLine(lineId) {
    return {
        type: VOTEUP_LINE,
        payload: LinesAPI.voteUp(lineId),
        meta: [ VOTEUP_LINE_PENDING, VOTEUP_LINE_FAILURE ]
    };
}

export function voteDownLine(lineId) {
    return {
        type: VOTEDOWN_LINE,
        payload: LinesAPI.voteDown(lineId),
        meta: [ VOTEDOWN_LINE_PENDING, VOTEDOWN_LINE_FAILURE ]
    };
}

export function flagLine(lineId) {
    return {
        type: FLAG_LINE,
        payload: LinesAPI.flag(lineId),
        meta: [ FLAG_LINE_PENDING, FLAG_LINE_FAILURE ]
    };
}

export function changePage(page) {
    return {
        type: CHANGE_PAGE,
        payload: LinesAPI.getLines(page),
        meta: [ CHANGE_PAGE_PENDING, CHANGE_PAGE_FAILURE ]
    };
}
