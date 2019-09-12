import React from 'react';

const Footer = () => {

    return (
        <footer className="main-footer">{/* Main Footer */}
            {/* To the right */}
            {/* Default to the left */}
            <span className="hidden-xs"><strong>Copyright &copy; <span id="yearnow">{new Date().getFullYear()}</span> <a href="#">Delsan Office System Corporation</a>.</strong> All rights reserved.</span>
            <div className="pull-right">Version DBIC 180320</div>
        </footer>
    );
};

export default Footer;