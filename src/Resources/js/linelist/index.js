import React from 'react';
import { combineReducers, createStore } from 'redux';
import { Provider } from 'react-redux';
import LineList from './containers/LineList';
import * as reducers from './reducers';

/**
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */

let app = combineReducers(reducers);
let store = createStore(app, window.__INITIAL_STATE__);

React.render(<Provider store={store}>
    {() => <LineList />}
</Provider>, document.getElementById('linelist'));
