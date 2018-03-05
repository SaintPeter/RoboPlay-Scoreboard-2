require('./bootstrap');

import React from 'react';
import { render } from 'react-dom';

import Master from "./components/Master";

if (document.getElementById('scorer')) {
    render((
       <Master />
    ), document.getElementById('scorer'));
} else {
    console.log('Error:  Cannot find #scorer element');
}
