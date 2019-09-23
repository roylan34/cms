import React from "react";
import { isEmpty, makeId } from "../../helpers/utils";
import Cookies from '../../helpers/cookies';

export default function CurrentHeaderSearch(props) {

    return (
        <React.Fragment>
            <thead className="dt-head-search" style={{ display: "none" }}>
                {/** Custom header search */}
                <tr>
                    <th className="text-center" colSpan="">
                        <div className="row">
                            <ul className="col-md-12 col-sm-12 list-inline">
                                <li className="col-md-2 col-sm-12 col-xs-12 dt-searchfield">
                                    <select
                                        name="delsan_comp"
                                        // onChange={this.props.onSearch}
                                        // value={this.props.stateVal.delsan_comp}
                                        id="search-company-delsan"
                                        className="form-control"
                                    >
                                        <option>---</option>
                                        <option value="dosc">DOSC</option>
                                        <option value="dbic">DBIC</option>
                                    </select>
                                </li>
                            </ul>
                        </div>
                    </th>
                </tr>
            </thead>
        </React.Fragment>
    );
}
