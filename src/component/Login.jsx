import React, { useState, useEffect } from 'react';
import { Input, Button, Icon } from 'antd';
import $ from 'jquery';
import logo from '../assets/img/delsanlogo.png';
import banner from '../assets/img/cms-banner.jpg';
import dbiclogo from '../assets/img/dbic-logo.jpg';
import { BASE_URL } from '../helpers/constant';
import { withRouter } from 'react-router-dom';
import Cookies from '../helpers/cookies.js';
import auth from '../helpers/auth';
import './login.css';

export default function Login() {

    function handleChange(e) {
        setCred({
            [e.target.name]: e.target.value
        });
    }

    const [cred, setCred] = useState({ username: '', passsword: '' });

    useEffect(() => {
        console.log({ ...cred });
    });

    return (
        < div id="wrapper" >
            {/* Navigation */}
            < div className="container-fluid" >
                <div className="login-box-body col-md-12">
                    <div className="row">
                        <div className="login-banner" style={{ 'paddingTop': 20 }}>
                            <img src={banner} className="img-responsive center-block" />
                        </div>{/* /.login-banner */}
                    </div>
                    <div className="row">
                        <div className="row col-md-6 col-md-offset-3" style={{ 'paddingTop': 20 }}>
                            <div className="col-md-6">
                                <p className="text-center">This product is licensed to:</p>
                                <img src={logo} className="img-responsive center-block company-logo" width="auto" />
                            </div>
                            <div className="col-md-5">
                                <h3 className="login-box-msg text-center">Login Page</h3>
                                <form id="frmLogin">
                                    <div className="form-group">
                                        <Input placeholder="Username"
                                            prefix={<Icon type="user" style={{ color: 'rgba(0,0,0,.25)' }} />}
                                            onChange={handleChange}
                                            name="username"
                                        />
                                    </div>
                                    <div className="form-group">
                                        <Input.Password placeholder="Password"
                                            prefix={<Icon type="key" style={{ color: 'rgba(0,0,0,.25)' }} />}
                                            onChange={handleChange}
                                            name="password"
                                        />
                                    </div>
                                    <div className="row">
                                        <div className="col-md-5 col-md-offset-7">
                                            <Button type="primary" className="col-sm-12 col-xs-12" id="btnLogin">Login</Button>
                                        </div>{/* /.col */}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="col-md-12">
                    <hr align="center" width="60%" style={{ backgroundColor: '#999', borderWidth: 0, color: '#999', height: 2, lineHeight: 0 }} />
                </div>
                <div className="row">
                    <div className="col-md-4 col-md-offset-2">
                        <img src={dbiclogo} className="img-responsive center-block company-logo" width="auto" />
                    </div>
                    <div className="col-md-4" style={{ 'fontSize': 12 }}>
                        <div >
                            <strong>Copyright &copy; <span id="yearnow">{new Date().getUTCFullYear()}</span> <a href="#">Delsan Business Innovations Corporation</a>.</strong> All rights reserved.
                            </div>
                    </div>
                </div>
            </div >
            {/* /container-fluid */}
        </div >
    );
}