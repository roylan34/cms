import React, { useEffect, useState } from "react";
import { Form, Input, DatePicker, Select, Row, Col } from "antd";
import { SelectCategory } from '../../helpers/dropdown';

function ArchiveHeaderSearch(props) {

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
                <div className="">

                    <Row type="flex" gutter={[8, 8]} align="middle" justify='space-around' className="search">
                        <Col xs={24} sm={12} md={4}>
                            <Form.Item>
                                <Input placeholder="Company" onChange={handleSearchInput} name="comp" value={stateSearch.comp} />
                            </Form.Item>
                        </Col>
                        <Col xs={24} sm={12} md={4}>
                            <Form.Item>
                                <SelectCategory placeholder="Select category" onChange={handleCategory} name="category" value={stateSearch.category} />
                            </Form.Item>
                        </Col>
                        <Col xs={24} sm={12} md={4}>
                            <Form.Item>
                                <DatePicker style={{ width: '100%' }} placeholder="Valid from" onChange={handleValidFrom} name="valid_from" value={stateSearch.valid_from} />
                            </Form.Item>
                        </Col>
                        <Col xs={24} sm={12} md={4}>
                            <Form.Item>
                                <DatePicker style={{ width: '100%' }} placeholder="Valid to" onChange={handleValidTo} name="valid_to" value={stateSearch.valid_to} />
                            </Form.Item>
                        </Col>
                        <Col xs={24} sm={12} md={4}>
                            <Form.Item>
                                <Select style={{ width: '100%' }} placeholder="Status" onChange={handleStatus} name="status" value={stateSearch.status}>
                                    <Option value="closed">CLOSED</Option>
                                    <Option value="cancel">CANCEL</Option>
                                </Select>
                            </Form.Item>
                        </Col>
                        <Col>
                            <Form.Item><button className="btn btn-sm btn-flat btn-primary" onClick={handleSearch}>Search</button></Form.Item>
                        </Col>
                        <Col>
                            <Form.Item><button className="btn btn-sm btn-flat btn-warning" onClick={handleClear}>Clear</button></Form.Item>
                        </Col>
                    </Row>
                </div>

            </caption>
        </React.Fragment>
    );
}

const WrappedCurrentSearch = ArchiveHeaderSearch;
export default React.memo(WrappedCurrentSearch);
