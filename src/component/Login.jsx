import React from 'react';
import { useHistory } from 'react-router-dom';
import { Input, Button, Icon, Form, Notification, Modal } from 'antd';
import $ from 'jquery';
import logo from '../assets/img/delsanlogo.png';
import banner from '../assets/img/cms-banner.jpg';
import dbiclogo from '../assets/img/dbic-logo.jpg';
import { API_URL } from '../helpers/constant';
import Cookies from './../helpers/cookies';
import './login.css';

function Login(props) {

    const { getFieldDecorator } = props.form;
    const history = useHistory();

    function submitData(data) {
        $.ajax({
            url: `${API_URL}/login.php`,
            data: { action: 'login', username: data.username, password: data.password },
            dataType: 'json',
            xhrFields: {
                withCredentials: true
            },
            crossDomain: true,
            method: "POST",
            success: (res) => {
                if (res.status === "success") {
                    //Redirect to current page.
                    Cookies.set('samplebiscuit', 1, 4);
                    history.push('/current');
                }
                else if (res.status === "inactive") {
                    Modal.warning({ title: 'Authentication failed!', content: (<div>Account has been disabled.</div>) });
                }
                else if (res.status === "invalid") {
                    Modal.warning({ title: 'Authentication failed!', content: "Invalid login account. Please try again" });
                }
            },
            error: (xhr, status) => {
                Modal.error({ title: 'Error', content: "Something went wrong! Can't login." });
            }
        });
    }

    function onSubmit(e) {
        e.preventDefault();
        props.form.validateFields((err, val) => {
            if (!err) {
                submitData(val);
            }
        });
    }

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
                                <form id="frmLogin" onSubmit={onSubmit}>
                                    <div className="form-group">
                                        {
                                            getFieldDecorator('username', {
                                                rules: [{ required: true }]
                                            })(<Input placeholder="Username"
                                                prefix={<Icon type="user" style={{ color: 'rgba(0,0,0,.25)' }} />}
                                                name="username"
                                            />)
                                        }

                                    </div>
                                    <div className="form-group">
                                        {
                                            getFieldDecorator('password', {
                                                rules: [{ required: true }]
                                            })(<Input.Password placeholder="Password"
                                                prefix={<Icon type="key" style={{ color: 'rgba(0,0,0,.25)' }} />}
                                                name="password"
                                                visibilityToggle={false}
                                            />)
                                        }

                                    </div>
                                    <div className="row">
                                        <div className="col-md-5 col-md-offset-7">
                                            <Button type="primary" className="col-sm-12 col-xs-12" htmlType="submit">Login</Button>
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

const WrappedLogin = Form.create()(Login);
export default WrappedLogin;