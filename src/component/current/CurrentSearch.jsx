import React, { useState } from "react";
import { Input, DatePicker, Select, Row, Col } from "antd";
import { SelectCategory } from '../../helpers/dropdown';

function CurrentHeaderSearch(props) {

    const { Option } = Select;
    const initialState = { comp: "", category: "", valid_from: null, valid_to: null, status: "" };
    const [stateSearch, setStateSearch] = useState(initialState);

    function handleSearch(e) {
        e.preventDefault();
        let hasSearchKey = Object.keys(stateSearch).length;
        let hasSearchValue = Object.values(stateSearch).some(val => val !== "" || val !== undefined);
        if (hasSearchKey > 0 && hasSearchValue) {
            props.getSearchValue(stateSearch);
            props.refreshTable();
        }
    }

    function handleClear() {
        props.getSearchValue(initialState);
        setStateSearch(initialState);
        props.refreshTable();
    }
    function handleSearchInput(e) {
        const val = e.target.value;
        const name = e.target.name;
        setStateSearch(prevState => ({
            ...prevState, [name]: val
        }));
    }
    function handleCategory(val, opt) {
        setStateSearch(prevState => ({
            ...prevState,
            category: val || null
        }));
    }
    function handleStatus(val, opt) {
        setStateSearch(prevState => ({
            ...prevState,
            status: val || null
        }));
    }
    function handleValidFrom(momentObj) {
        setStateSearch(prevState => ({
            ...prevState,
            valid_from: momentObj
        }));
    }
    function handleValidTo(momentObj) {
        setStateSearch(prevState => ({
            ...prevState,
            valid_to: momentObj
        }));
    }

    console.log('render search');

    return (
        <React.Fragment>
            <caption className="dt-head-search" style={{ captionSide: 'top' }}>
                {/** Custom header search */}
                <Row type="flex" gutter={[8, 8]} align="middle" justify='space-around' className="search">
                    <Col xs={24} sm={12} md={4}>
                        <Input placeholder="Company" onChange={handleSearchInput} name="comp" value={stateSearch.comp} />
                    </Col>
                    <Col xs={24} sm={12} md={4}>
                        <SelectCategory placeholder="Select category" onChange={handleCategory} name="category" value={stateSearch.category} />
                    </Col>
                    <Col xs={24} sm={12} md={4}>
                        <DatePicker style={{ width: '100%' }} placeholder="Valid from" onChange={handleValidFrom} name="valid_from" value={stateSearch.valid_from} />
                    </Col>
                    <Col xs={24} sm={12} md={4}>
                        <DatePicker style={{ width: '100%' }} placeholder="Valid to" onChange={handleValidTo} name="valid_to" value={stateSearch.valid_to} />
                    </Col>
                    <Col xs={24} sm={12} md={4}>
                        <Select style={{ width: '100%' }} placeholder="Status" onChange={handleStatus} name="status" value={stateSearch.status}>
                            <Option value="active">ACTIVE</Option>
                            <Option value="notify">NOTIFYING</Option>
                            <Option value="expired">EXPIRED</Option>
                        </Select>
                    </Col>
                    <Col>
                        <button className="btn btn-sm btn-success" onClick={handleSearch}>Search</button>
                    </Col>
                    <Col>
                        <button className="btn btn-sm btn-warning" onClick={handleClear}>Clear</button>
                    </Col>
                </Row>
            </caption>
        </React.Fragment>
    );
}

const WrappedCurrentSearch = CurrentHeaderSearch;
export default React.memo(WrappedCurrentSearch);
