import React from 'react';


function NotFound(props) {

    return (
        <div className="error-page">
            <h2 className="headline text-yellow"> 404</h2>

            <div className="error-content">
                {/* <h3><i className="fa fa-warning text-yellow"></i> Oops! Page not found.</h3> */}
                <h3><i className="fa fa-warning text-yellow"></i> No match for <code>{props.location.pathname}</code></h3>
                <p>
                    We could not find the page you were looking for.
                    Meanwhile, you may <a href="#">return to dashboard.</a>
                </p>

                <form className="search-form">
                    <div className="input-group">
                        <input type="text" name="search" className="form-control" placeholder="Search" />

                        <div className="input-group-btn">
                            <button type="button" name="submit" className="btn btn-warning btn-flat"><i className="fa fa-search"></i>
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    )
}

export default NotFound;