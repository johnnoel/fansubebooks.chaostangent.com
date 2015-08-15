import React from 'react';
import { createStore, applyMiddleware } from 'redux';
import { Provider } from 'react-redux';
import mapValues from 'lodash/object/mapValues';
import promiseMiddleware from './middleware/promise';
import LineList from './containers/LineList';
import app from './reducers';

/**
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
export default class LineListComponent {
    constructor(options) {
        let mw = applyMiddleware(promiseMiddleware)(createStore),
            store = mw(app, this.getInitialState(options));

        React.render(<Provider store={store}>
            {() => <LineList />}
        </Provider>, options.element);
    }

    getInitialState(options) {
        let defaultState = {
            lines: [],
            page: 1,
            pages: 1
        };

        return mapValues(defaultState, (v, k, o) => {
            if (k in options) {
                return options[k];
            }

            return v;
        });
    }
}
