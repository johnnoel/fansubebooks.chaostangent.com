import React from 'react';
import { createStore, applyMiddleware } from 'redux';
import { Provider } from 'react-redux';
import promiseMiddleware from './middleware/promise';
import LineList from './containers/LineList';
import app from './reducers';

/**
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */

let mw = applyMiddleware(promiseMiddleware)(createStore);
let store = mw(app, window.__INITIAL_STATE__);

React.render(<Provider store={store}>
    {() => <LineList />}
</Provider>, document.getElementById('linelist'));
