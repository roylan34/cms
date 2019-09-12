import React, { Suspense, lazy } from 'react';
import { BrowserRouter, Route, Switch, Redirect } from 'react-router-dom';
import { Provider } from 'react-redux';
import store from './store';
import Login from './component/Login';
import Main from "./component/layout/main";
import NewDoc from './component/new-doc/NewDoc';
import tmplPma from './component/templates/Pma';

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

const TemplateRoute = ({ component: Component, headTitle: headTitle, ...rest }) => {
    return (
        <Route {...rest}>
            <Component />
        </Route>
    )
}
function App() {
    return (
        <Provider store={store}>
            <BrowserRouter basename="/cms">
                <Switch>
                    <LoginRoute exact path="/login" component={Login} />
                    <DashboardRoute path="/new-doc" component={NewDoc} headTitle="New Documents" />
                    <TemplateRoute path="/template/pma" component={tmplPma} headTitle="Printer Management Agreement" />
                </Switch>
            </BrowserRouter>
        </Provider>
    );
}

export default App;
