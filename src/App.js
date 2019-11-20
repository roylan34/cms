import React, { Suspense, lazy } from 'react';
import { BrowserRouter, Route, Switch, Redirect } from 'react-router-dom';
import { Provider } from 'react-redux';
import store from './store';
import Login from './component/Login';
import Main from "./component/layout/main";
import Dashboard from './component/dashboard/Dashboard';
import NotFound from './component/NotFound';

import CurrentContract from './component/current/CurrentContract';
import ArchiveContract from './component/archive/ArchiveContract';

const tmplPMA = () => <div>PM Template</div>;


const LoginRoute = ({ ...rest }) => {

    return (
        <Route {...rest}>
            {/* Code that verify is already login then redirect to Contracts page. */}
        </Route>
    )
}

const DashboardRoute = ({ component: Component, headTitle: headTitle, ...rest }) => {
    return (
        <Route {...rest}>
            <Main headTitle={headTitle}>
                <Component />
            </Main >
        </Route>
    )
}

function App() {
    return (
        <Provider store={store}>
            <BrowserRouter basename="/cms">
                <Switch>
                    <LoginRoute exact path="/login" component={Login} />
                    <DashboardRoute path="/dashboard" component={Dashboard} headTitle="Dashboard" />
                    <DashboardRoute path="/current" component={CurrentContract} headTitle="Current" />
                    <DashboardRoute path="/archive" component={ArchiveContract} headTitle="Archive" />
                    <Route path="*" component={NotFound} />
                </Switch>
            </BrowserRouter>
        </Provider>
    );
}

export default App;
