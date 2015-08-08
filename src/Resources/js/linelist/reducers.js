import { CHANGE_PAGE } from './actions';

/**
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */

export function page(state = 1, action) {
    if (action) {
        switch (action.type) {
            case CHANGE_PAGE:
                return action.payload;
        }
    }

    return state;
}

export function pages(state = 1, action) {
    //switch (action.type) {
    //}

    return state;
}

export function lines(state = [], action) {
    //switch (action.type) {
    //}

    return state;
}
