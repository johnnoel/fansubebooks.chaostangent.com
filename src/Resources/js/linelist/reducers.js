import map from 'lodash/collection/map';
import clone from 'lodash/lang/clone';

import { VOTEUP_LINE, VOTEDOWN_LINE, CHANGE_PAGE } from './actions';

/**
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */

function page(state = 1, action) {
    switch (action.type) {
        case CHANGE_PAGE:
            return state;
        default:
            return state;
    }
}

function pages(state = 1, action) {
    //switch (action.type) {
    //}

    return state;
}

function lines(state = [], action) {
    switch (action.type) {
        case VOTEUP_LINE:
            let votedUpId = action.payload;
            return map(state, (line) => {
                let d = clone(line, true);

                if (line.id == votedUpId) {
                    d.positive_vote_count++;
                }

                return d;
            });
        case VOTEDOWN_LINE:
            let votedDownId = action.payload;
            return map(state, (line) => {
                let d = clone(line, true);

                if (line.id == votedDownId) {
                    d.negative_vote_count++;
                }

                return d;
            });

        default:
            return state;
    }
}

export default function root(state = { page: 1, pages: 1, lines: [] }, action) {
    switch (action.type) {
        // change_page changes both the page number and the line set
        case CHANGE_PAGE:
            return {
                page: page(action.payload.page, action),
                pages: pages(state.pages),
                lines: lines(action.payload.lines, action)
            };
        default:
            return {
                page: page(state.page, action),
                pages: pages(state.pages, action),
                lines: lines(state.lines, action)
            };
    }
}
