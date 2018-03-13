require('./bootstrap');

import React from 'react';
import { render } from 'react-dom';
import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import { Provider } from 'react-redux';

import Master from "./components/Master";

import reducer from "./reducers/reducer";

let store = createStore(
    reducer,
    applyMiddleware(thunk)
);

if (document.getElementById('scorer')) {
    render((
        <Provider store={store}>
            <Master />
        </Provider>
        ), document.getElementById('scorer'));
} else {
    console.log('Error:  Cannot find #scorer element');
}
