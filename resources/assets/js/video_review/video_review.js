require('./bootstrap');

import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';

import Master from "./components/Master";

import { store, persistor } from './configureStore';

if (document.getElementById('video_review')) {
  render((
    <Provider store={store}>
        <Master />
    </Provider>
  ), document.getElementById('video_review'));
} else {
  console.log('Error:  Cannot find #video_review element');
}
