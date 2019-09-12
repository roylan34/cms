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
                        <a href="#"><i className="fa fa-circle text-success"></i> Online</a>
                    </div>
                </div>

                {/* Sidebar Menu */}
                <ul className="sidebar-menu">
                    <li className="header">MAIN NAVIGATION</li>
                    {/* Optionally, you can add icons to the links */}
                    <li id="home">
                        <NavLink to="/new-doc"><span className="btn btn-warning">NEW DOCUMENTS</span></NavLink >
                    </li>
                    <li id="sidebar-contract" className="treeview"><a href="#"><i className="fa fa-pencil-square-o"></i> <span>Contracts</span> <i className="fa fa-angle-left pull-right"></i></a>
                        <ul id="contract" className="treeview-menu">
                            <li id='contract-current'><a href="#/dashboard"><i className="fa fa-circle-o"></i>Current</a></li>
                            <li id='contract-archive'><a href="#/inventory/dashboard"><i className="fa fa-circle-o"></i>Archive</a></li>
                        </ul>
                    </li>
                    <li id="drafts">
                        <a href="#"><i className="fa fa-hdd-o"></i><span>Drafts</span></a>
                    </li>
                    <li id="accounts">
                        <a href="#"><i className="fa fa-address-book"></i><span>Accounts</span></a>
                    </li>
                    <li id="sidebar-settings" className="treeview"><a href="#/settings"><i className="fa fa-cogs"></i> <span>Settings</span> <i className="fa fa-angle-left pull-right"></i></a>
                        <ul id="settings" className="treeview-menu">
                            <li id='settings-mif'><a href="#/settings"><i className="fa fa-circle-o"></i>MIF</a></li>
                            <li id='settings-inventory'><a href="#/inventory/settings"><i className="fa fa-circle-o"></i>Inventory</a></li>
                            <li id='settings-mrf'><a href="#/mrf/settings"><i className="fa fa-circle-o"></i>MRF</a></li>
                        </ul>
                    </li>
                </ul>
                {/* /.sidebar-menu */}
            </section>
            {/* /.sidebar */}
        </aside>
    )
}

export default SideBar;