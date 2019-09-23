import React from 'react';
import { Result, Button } from "antd";
import { withRouter } from 'react-router-dom';

function NotFound(props) {

    function backToHome() {
        props.history.push('/dashboard');
    }

    return (
        <Result
            status="404"
            title="404 NOT FOUND"
            subTitle="Sorry, the page you visited does not exist."
            extra={<Button type="primary" onClick={backToHome}>Back to Home</Button>}
        />

    )
}

export default withRouter(NotFound);