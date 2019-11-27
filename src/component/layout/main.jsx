import React, { useEffect } from 'react';
import { useDispatch } from 'react-redux';
import SideBar from './sideBar';
import Footer from './footer';
import Navigation from './navigation';
import Cookies from './../../helpers/cookies';
import { isEmpty } from './../../helpers/utils';

export default function Main(props) {

    const dispatch = useDispatch();
    useEffect(() => {
        //Change head title on every user page navigate.
        if (typeof props.headTitle !== "string") {
            throw new Error('Head Title must be string.');
        }
        if (isEmpty(props.headTitle)) {
            document.title = "Contract Management System";
        }
        document.title = `CMS - ${props.headTitle}`;
        dispatch({ type: 'LOGIN_USER_DETAILS' });
    });

    function accountDetails() {
        return {
            fullname: Cookies.get('fullname'),
            userrole: Cookies.get('user_role')
        };
    }
    return (
        <div id="theme">

            <div className="wrapper">
                <div id="include-navigation">
                    <Navigation />
                </div>
                {/* Content Wrapper. Contains page content */}
                <div className="content-wrapper">
                    {/* Content Header (Page header) */}
                    <section className="content-header">
                        <h1></h1>
                        {/*<ol className="breadcrumb">
                                <li><i className="fa fa-dashboard active"></i> Home</li>
                                <li className="active"></li>
                            </ol> */}
                    </section>

                    {/* Main content */}
                    <section className="content">
                        {/* Your Page Content Here */}
                        <div className="view-content">
                            {
                                props.children
                            }
                        </div>
                    </section>
                    {/* /.content */}
                </div>
                {/* /.content-wrapper */}

                <div className="scrollToTop"></div>
                <div id="include-left-sidebar">
                    <SideBar />
                </div>
                <div id="include-footer">
                    <Footer />
                </div>

            </div>
            {/* ./wrapper */}
        </div>
    );
}

