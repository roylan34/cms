import React from 'react';

const Footer = () => {

    return (
        <footer className="main-footer">{/* Main Footer */}
            {/* To the right */}
            {/* Default to the left */}
            <span className="hidden-xs">Copyright &copy; <span id="yearnow">{new Date().getFullYear()}</span> Delsan Office System Corporation All rights reserved.</span>
            <div className="pull-right">Version DBIC 180320</div>
        </footer>
    );
};

export default Footer;