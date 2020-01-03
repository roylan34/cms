import React from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { useHistory } from 'react-router-dom';
import dbiclogo from '../../assets/img/dbic-logo.jpg';
import avatar from '../../assets/img/avatar.svg';
import Auth from '../../helpers/auth';
import moment from 'moment';

function Navigation() {
    const { fullname, email } = useSelector(state => state.userDetailsReducer);
    const history = useHistory();
    const dispatch = useDispatch();

    function logout(e) {
        e.preventDefault();
        dispatch({ type: 'LOGOUT_USER_DETAILS' });
        Auth.logout(history);
    }
    return (
        <header className="main-header">
            {/* Main Header */}
            {/* Logo */}
            <div className="logo">
                {/* mini logo for sidebar mini 50x50 pixels */}
                <span className="logo-mini"><b>CMS</b></span>
                {/* logo for regular state and mobile devices */}
                <span className="logo-lg"><img src={dbiclogo} alt="DBIC logo" width="230" height="50" /></span>
            </div>

            {/* Header Navbar */}
            <nav className="navbar navbar-static-top" role="navigation">
                {/* Sidebar toggle button*/}
                <a href="#" className="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span className="sr-only">Toggle navigation</span>
                </a>
                {/* Navbar Right Menu */}
                <div className="hidden-xs" style={{ display: 'inline-block', lineHeight: 4, color: '#fff', fontSize: 13 }}><i className="fa fa-calendar-o" aria-hidden="true"></i> {moment().format('dddd, MMMM D YYYY')}</div>
                <div className="navbar-custom-menu">
                    <ul className="nav navbar-nav">
                        {/* User Account Menu */}
                        <li><a><i className="fa fa-bell-o" aria-hidden="true"></i></a></li>
                        <li className="dropdown user user-menu">
                            {/* Menu Toggle Button */}
                            <a href="" className="dropdown-toggle" data-toggle="dropdown">
                                {/* The user image in the navbar*/}
                                <img src={avatar} className="user-image" alt="User Image" />
                                {/* hidden-xs hides the username on small devices so only the image appears. */}
                                <span className="hidden-xs">&nbsp;</span>
                            </a>
                            <ul className="dropdown-menu">
                                {/* The user image in the menu */}
                                <li className="user-header">
                                    <img src={avatar} className="img-circle" alt="User Image" />

                                    <p className="info">{/* <small>Member since Nov. 2012</small> */}
                                        {fullname}
                                        <small>{email}</small>
                                    </p>
                                </li>

                                {/* Menu Footer*/}
                                <li className="user-footer" style={{ 'backgroundColor': '#333' }}>
                                    <div className="pull-left">
                                        <a href="#" id="change-pass" className="btn btn-warning btn-flat" data-toggle="modal" data-target="#modalChangePass">Change password</a>
                                    </div>
                                    <div className="pull-right">
                                        <button id="logout" className="btn btn-sm btn-warning btn-flat" onClick={logout}><i className="fa fa-sign-out" aria-hidden="true"></i> Log out</button>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        {/* Control Sidebar Toggle Button */}
                        <li>
                            <div className="col-md-2"></div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

    );
};


export default Navigation;