import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import * as serviceWorker from './serviceWorker';


//CSS Files
import '../node_modules/bootstrap/dist/css/bootstrap.min.css';
import './assets/layouts/css/AdminLTE.min.css';  //Template style 
import './assets/layouts/css/skins/skin-green.min.css'; //AdminLTE Skins use className skin-green
import '../node_modules/datatables.net-bs/css/dataTables.bootstrap.min.css';
import '../node_modules/datatables.net-responsive-bs/css/responsive.bootstrap.min.css';
import '../node_modules/datatables.net-buttons-bs/css/buttons.bootstrap.min.css';
import './assets/css/cms.css';
import './assets/css/media.css';

//JS
import $ from 'jquery';
import './assets/layouts/js/app.js';//AdminLTE App 
import 'bootstrap';

//Global scope
window.jQuery = $;
window.$ = $;
global.jQuery = $;


ReactDOM.render(<App />, document.getElementById('root'));

// If you want your app to work offline and load faster, you can change
// unregister() to register() below. Note this comes with some pitfalls.
// Learn more about service workers: https://bit.ly/CRA-PWA
serviceWorker.unregister();
