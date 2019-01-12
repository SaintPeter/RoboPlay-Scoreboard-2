require('./bootstrap');

import React from 'react';
import { render } from 'react-dom';
import { PersistGate } from 'redux-persist/integration/react'
import { Provider } from 'react-redux';

import Master from "./components/Master";

import { store, persistor } from './configureStore';

if (document.getElementById('scorer')) {
    render((
        <Provider store={store}>
            <PersistGate loading={null} persistor={persistor}>
                <Master />
            </PersistGate>
        </Provider>
        ), document.getElementById('scorer'));
} else {
    console.log('Error:  Cannot find #scorer element');
}
