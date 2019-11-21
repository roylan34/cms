import React from 'react';
import avatar from '../../assets/img/avatar.png';
import { Icon } from 'antd';
import { BrowserRouter as Router, Route, NavLink } from "react-router-dom";

const SideBar = (props) => {

    return (
        <aside className="main-sidebar">
            {/* Left side column. contains the logo and sidebar */}

            {/* sidebar: style can be found in sidebar.less */}
            <section className="sidebar">

                {/* Sidebar user panel (optional) */}
                <div className="user-panel user">
                    <div className="pull-left image">
                        <img src={avatar} className="img-circle" alt="User Image" />
                    </div>
                    <div className="pull-left info">
                        <p>{props.fullname}</p>
                        {/* Status */}
                        <a href="#"><i className="fa fa-circle text-success"></i>Online</a>
                    </div>
                </div>

                {/* Sidebar Menu */}
                <ul className="sidebar-menu">

                    <li id='dashboard'><NavLink to="/dashboard"><i className="fa fa-calendar" aria-hidden="true"></i>DASHBOARD</NavLink></li>
                    <li className="header">CONTRACTS</li>
                    <li id='contract-current'><NavLink to="/current"><i className="fa fa-pencil-square-o"></i>Current</NavLink></li>
                    <li id='contract-archive'><NavLink to="/archive"><i className="fa fa-archive"></i>Archive</NavLink></li>
                    <li className="header">MISC</li>
                    <li id='accounts'><NavLink to="/account"><i className="fa fa-address-book"></i>Accounts</NavLink></li>
                    <li id="settings"><a href="#"><i className="fa fa-cogs"></i><span>Settings</span></a></li>
                </ul>
                {/* /.sidebar-menu */}
            </section>
            {/* /.sidebar */}
        </aside >
    )
}

export default SideBar;