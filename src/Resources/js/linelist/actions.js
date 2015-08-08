/**
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */

export const VOTEUP_LINE = 'VOTEUP_LINE';
export const VOTEDOWN_LINE = 'VOTEDOWN_LINE';
export const FLAG_LINE = 'FLAG_LINE';
export const CHANGE_PAGE = 'CHANGE_PAGE';

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
        type: CHANGE_PAGE,
        payload: page
    };
}
