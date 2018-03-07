require('./bootstrap');

import React from 'react';
import { render } from 'react-dom';
import { createStore } from 'redux';
import { Provider } from 'react-redux';

import Master from "./components/Master";

import reducer from "./reducers/reducer";

let store = createStore(reducer);

if (document.getElementById('scorer')) {
    render((
        <Provider store={store}>
            <Master />
        </Provider>
        ), document.getElementById('scorer'));
} else {
    console.log('Error:  Cannot find #scorer element');
}
