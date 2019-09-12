import React from 'react';
import '../assets/css/page-loader.css';

export default function pageLoader() {
    return (
        <div>
            <div id="machine-backDrop">
                <div className="text-center backDrop-loader"><i className="fa fa-3x fa-refresh fa-spin"></i>Loading ...</div>
            </div>
        </div>
    );
}