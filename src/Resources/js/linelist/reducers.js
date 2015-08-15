import map from 'lodash/collection/map';
import clone from 'lodash/lang/clone';
import LinesAPI from './api';

import { VOTEUP_LINE, VOTEDOWN_LINE, FLAG_LINE, CHANGE_PAGE, CHANGE_PAGE_PENDING } from './actions';

/**
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */

function page(state = 1, action) {
    return state;
}

function pages(state = 1, action) {
    return state;
}

function lines(state = [], action) {
    switch (action.type) {
        case VOTEUP_LINE:
            let votedUpId = action.payload;
            return map(state, line => {
                let d = clone(line, true);

                if (line.id == votedUpId) {
                    d.positive_vote_count++;
                    d.user_positive_vote = true;
                }

                return d;
            });
        case VOTEDOWN_LINE:
            let votedDownId = action.payload;
            return map(state, line => {
                let d = clone(line, true);

                if (line.id == votedDownId) {
                    d.negative_vote_count++;
                    d.user_negative_vote = true;
                }

                return d;
            });
        case FLAG_LINE:
            let flagId = action.payload;
            return map(state, line => {
                let d = clone(line, true);

                if (line.id == flagId) {
                    d.user_flag = true;
                }

                return d;
            });
        default:
            return state;
    }
}

function fetchingLines(state = false, action) {
    switch (action.type) {
        case CHANGE_PAGE_PENDING:
            return true;
        case CHANGE_PAGE:
            return false;
        default:
            return state;
    }
}

const initialState = {
    page: 1,
    pages: 1,
    lines: [],
    fetchingLines: false
};

export default function root(state = initialState, action) {
    switch (action.type) {
        case '@@redux/INIT':
            return {
                page: page(state.page, action),
                pages: pages(state.pages, action),
                lines: LinesAPI.mergeUserVotes(state.lines),
                fetchingLines: fetchingLines(state.fetchingLines, action)
            };
        // change_page changes both the page number and the line set
        case CHANGE_PAGE:
            return {
                page: page(action.payload.page, action),
                pages: pages(state.pages, action),
                lines: lines(action.payload.lines, action),
                fetchingLines: fetchingLines(state.fetchingLines, action)
            };
        default:
            return {
                page: page(state.page, action),
                pages: pages(state.pages, action),
                lines: lines(state.lines, action),
                fetchingLines: fetchingLines(state.fetchingLines, action)
            };
    }
}
