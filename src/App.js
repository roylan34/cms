import React, { Suspense, lazy } from 'react';
import { BrowserRouter, Route, Switch, Redirect } from 'react-router-dom';
import { Provider } from 'react-redux';
import store from './store';
import Login from './component/Login';
import Main from "./component/layout/main";
import Dashboard from './component/dashboard/Dashboard';
import NotFound from './component/NotFound';
import Auth from "./helpers/auth";
import $ from 'jquery';

import CurrentIndex from './component/current/CurrentIndex';
import ArchiveIndex from './component/archive/ArchiveIndex';
import AccountIndex from './component/account/AccountIndex';
import CategoryIndex from './component/settings/CategoryIndex';


const LoginRoute = ({ component: Component, ...rest }) => {
    return (
        <Route {...rest} render={matchProps => (
            (
                Auth.isAuthenticated({ ...rest }) === false ?
                    <Component {...matchProps} />
                    :
                    <Redirect to="/current" />
            )
        )} />
    )
};

const DashboardRoute = ({ component: Component, headTitle: headTitle, ...rest }) => {
    return (
        <Route {...rest} render={(matchProps) => (
            (Auth.isAuthenticated({ ...rest }) === true ?
                <Main headTitle={headTitle}>
                    <Component />
                </Main >
                :
                <Redirect to="/login" />
            )

        )}>

        </Route>
    )
}

$.ajaxSetup({
    xhrFields: {
        withCredentials: true
    }
});
function App() {
    return (
        <Provider store={store}>
            <BrowserRouter basename="/cms">
                <Switch>
                    <Route exact path="/">
                        <Redirect to="/login" />
                    </Route>
                    <LoginRoute exact path="/login" component={Login} />
                    <DashboardRoute path="/dashboard" component={Dashboard} headTitle="Dashboard" />
                    <DashboardRoute path="/current" component={CurrentIndex} headTitle="Current" />
                    <DashboardRoute path="/archive" component={ArchiveIndex} headTitle="Archive" />
                    <DashboardRoute path="/account" component={AccountIndex} headTitle="Account" />
                    <DashboardRoute path="/settings" component={CategoryIndex} headTitle="Settings" />
                    <Route path="*" component={NotFound} />
                </Switch>
            </BrowserRouter>
        </Provider>
    );
}

export default App;
