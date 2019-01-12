require('./bootstrap');

import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';

import Master from "./components/Master";

import { store, persistor } from './configureStore';

if (document.getElementById('invoicer')) {
  render((
    <Provider store={store}>
        <Master />
    </Provider>
  ), document.getElementById('invoicer'));
} else {
  console.log('Error:  Cannot find #scorer element');
}
