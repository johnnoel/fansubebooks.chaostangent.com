import { CHANGE_PAGE } from './actions';

/**
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */

function page(state = 1, action) {
    if (action) {
        switch (action.type) {
            case CHANGE_PAGE:
                return state;
        }
    }

    return state;
}

function pages(state = 1, action) {
    //switch (action.type) {
    //}

    return state;
}

function lines(state = [], action) {
    //switch (action.type) {
    //}

    return state;
}

export default function root(state = { page: 1, pages: 1, lines: [] }, action) {
    switch (action.type) {
        case CHANGE_PAGE:
            return {
                page: page(action.payload.page, action),
                pages: pages(state.pages),
                lines: lines(action.payload.lines, action)
            };
        default:
            return state;
    }
}
