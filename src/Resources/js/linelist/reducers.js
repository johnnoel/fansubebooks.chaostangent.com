import { List } from 'immutable';
import assign from 'lodash/object/assign';
import LinesAPI from './api';

import { VOTEUP_LINE, VOTEDOWN_LINE, FLAG_LINE, CHANGE_PAGE, CHANGE_PAGE_PENDING } from './actions';

/**
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */

function lines(state, action) {
    switch (action.type) {
        case VOTEUP_LINE:
            var lines = state.lines.map(line => {
                if (line.id == action.payload) {
                    line.positive_vote_count++;
                    line.user_positive_vote = true;
                }

                return line;
            });

            return assign({}, state, { lines: lines });
        case VOTEDOWN_LINE:
            var lines = state.lines.map(line => {
                if (line.id == action.payload) {
                    line.negative_vote_count++;
                    line.user_negative_vote = true;
                }

                return line;
            });

            return assign({}, state, { lines: lines });
        case FLAG_LINE:
            var lines = state.lines.map(line => {
                if (line.id == action.payload) {
                    line.user_flag = true;
                }

                return line;
            });

            return assign({}, state, { lines: lines });
    }

    return state;
}

const initialState = {
    lines: List()
};

export default function root(state = initialState, action) {
    if (action.type == '@@redux/INIT') {
        if (!List.isList(state.lines)) {
            state.lines = LinesAPI.mergeUserVotes(List(state.lines));
        }
    }

    state = lines(state, action);

    return state;
}
