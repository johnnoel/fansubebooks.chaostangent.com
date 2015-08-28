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
    constructor(element, initialState) {
        if (!element) {
            return;
        }

        let mw = applyMiddleware(promiseMiddleware)(createStore),
            store = mw(app, initialState);

        React.render(<Provider store={store}>
            {() => <LineList />}
        </Provider>, element);
    }
}
