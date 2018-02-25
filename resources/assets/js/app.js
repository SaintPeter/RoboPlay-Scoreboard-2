require('./bootstrap');

import React from 'react';
import { render } from 'react-dom';

import Master from "./components/Master";

if (document.getElementById('example')) {
    render((
       <Master />
    ), document.getElementById('example'));
} else {
    console.log('Error:  Cannot find #example element');
}
