import React from 'react';
import { useSelector } from 'react-redux';
import avatar from '../../assets/img/avatar.png';
import { Icon } from 'antd';
import { NavLink } from "react-router-dom";

const SideBar = (props) => {
    const { fullname, user_role } = useSelector(state => state.userDetailsReducer);
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
                        <div>{fullname}</div>
                        <small>{user_role}</small>
                    </div>
                </div>

                {/* Sidebar Menu */}
                <ul className="sidebar-menu">

                    <li id='dashboard'><NavLink to="/dashboard"><i className="fa fa-calendar" aria-hidden="true"></i>DASHBOARD</NavLink></li>
                    <li className="header">CONTRACTS</li>
                    <li id='contract-current'><NavLink to="/current"><i className="fa fa-pencil-square-o"></i>Current</NavLink></li>
                    <li id='contract-archive'><NavLink to="/archive"><i className="fa fa-archive"></i>Archive</NavLink></li>
                    {
                        user_role === 'ADMIN' ?
                            <React.Fragment>
                                <li className="header">MISC</li>
                                <li id='accounts'><NavLink to="/account"><i className="fa fa-address-book"></i>Accounts</NavLink></li>
                                <li id='settings'><NavLink to="/settings"><i className="fa fa-cogs"></i>Settings</NavLink></li>
                            </React.Fragment>
                            : null

                    }

                </ul>
                {/* /.sidebar-menu */}
            </section>
            {/* /.sidebar */}
        </aside >
    )
}

export default SideBar;