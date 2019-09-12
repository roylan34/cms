import React, { Fragment, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Button } from 'antd';
import './fixed-header.less';

import { ReactComponent as Draftsvg } from './../../assets/img/draft.svg';
import { ReactComponent as Backsvg } from './../../assets/img/back.svg';

export default function FixedHeader(props) {

    function __componentDidMount() {
        document.body.classList.add('template-page-bg');
    }

    function __componentWillUnmount() {
        document.body.classList.remove('template-page-bg');
    }
    function downloadPdf() {
        window.URL = window.URL || window.webkitURL;  // Take care of vendor prefixes.
        var endpoint = 'http://localhost:4000/downloadpdf';

        fetch(endpoint).then(function (response) {
            return response.blob();
        }).then(function (blob) {
            console.log(blob);
            var a = document.createElement('a');
            var url = window.URL.createObjectURL(new Blob([blob], { type: 'application/pdf' }));
            a.href = url;
            a.download = 'report.pdf';
            a.click();
            window.URL.revokeObjectURL(url);
        });

        //Use native http request.
        // var xhr = new XMLHttpRequest();
        // xhr.open('GET', link, true);
        // xhr.responseType = 'blob';

        // xhr.onload = function (e) {
        //     if (this.status == 200) {
        //         var a = document.createElement('a');
        //         var url = window.URL.createObjectURL(new Blob([this.response], { type: 'application/pdf' }));
        //         a.href = url;
        //         a.download = 'report.pdf';
        //         a.click();
        //         window.URL.revokeObjectURL(url);
        //     } else {
        //         console.log(this.status);
        //         alert('Download failed...!  Please Try again!!!');
        //     }
        // };
        // xhr.send();
    }

    useEffect(() => {
        __componentDidMount();

        return __componentWillUnmount;
    });

    return (
        //use React.createPortal to render out export pdf outside of .root
        <Fragment>
            <div className="template-fixed-header">
                <div className="col-md-6 col-sm-12 col-xs-12">
                    <div className="col-md-2 col-sm-2 col-xs-6 back"><div><Backsvg width="18" height="20" /> <Link to="/new-doc">Back</Link></div></div>
                    <div className="col-md-9 col-sm-6 col-xs-6 template-name"><p>Printer Management Agreement</p></div>
                </div>
                <div className="col-md-6 col-sm-12">
                    <div className="col-md-offset-1 col-md-3 col-sm-4 col-xs-4"><button size="small" type="primary" onClick={downloadPdf}>Download as PDF</button></div>
                    <div className="col-md-3 col-sm-4 col-xs-4"><button size="small" type="warning"><Draftsvg width="20" height="20" style={{ opacity: 0.7 }} /> <p>Save as Draft</p></button></div>
                    <div className="col-md-5 col-sm-4 col-xs-4"><Button size="default" type="primary" className="btn-submit-contract">Submit</Button></div>
                </div>
            </div>
        </Fragment>
    )
}