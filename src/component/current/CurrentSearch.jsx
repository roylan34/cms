import React, { useEffect } from "react";
import { useDispatch } from "react-redux";
import { Form, Input, DatePicker, Select, Row, Col } from "antd";
import { SelectCategory } from '../../helpers/dropdown';

function CurrentHeaderSearch(props) {

    const { getFieldDecorator, resetFields, validateFields } = props.form;
    const { Option } = Select;
    const dispatch = useDispatch();

    function handleSearch(e) {
        e.preventDefault();
        validateFields((err, val) => {
            if (!err && Object.values(val).some(val => val !== undefined)) { //Trigger search only if least one of them has value.
                dispatch({ type: 'CURRENT_SEARCH', search: val });
            }
        });
    }

    function handleClear() {
        dispatch({ type: 'CURRENT_SEARCH', search: {} });
        resetFields();
    }

    useEffect(() => {
        console.log('render search');
    })

    return (
        <React.Fragment>
            <caption className="dt-head-search" style={{ captionSide: 'top' }}>
                {/** Custom header search */}
                <div className="">

                    <Row type="flex" gutter={[8, 8]} align="middle" justify='space-around' className="search">
                        <Col xs={24} sm={12} md={4}>
                            <Form.Item>
                                {getFieldDecorator('comp')(<Input placeholder="Company" />)}
                            </Form.Item>
                        </Col>
                        <Col xs={24} sm={12} md={4}>
                            <Form.Item>
                                {getFieldDecorator('category')(<SelectCategory placeholder="Select category" />)}
                            </Form.Item>
                        </Col>
                        <Col xs={24} sm={12} md={4}>
                            <Form.Item>
                                {getFieldDecorator('valid_from')(<DatePicker style={{ width: '100%' }} placeholder="Valid from" />)}
                            </Form.Item>
                        </Col>
                        <Col xs={24} sm={12} md={4}>
                            <Form.Item>
                                {getFieldDecorator('valid_to')(<DatePicker style={{ width: '100%' }} placeholder="Valid to" />)}
                            </Form.Item>
                        </Col>
                        <Col xs={24} sm={12} md={4}>
                            <Form.Item>
                                {getFieldDecorator('status')(
                                    <Select style={{ width: '100%' }} placeholder="Status">
                                        <Option value="active">ACTIVE</Option>
                                        <Option value="notify">NOTIFYING</Option>
                                        <Option value="expired">EXPIRED</Option>
                                    </Select>
                                )}
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

const WrappedCurrentSearch = Form.create()(CurrentHeaderSearch);
export default WrappedCurrentSearch;
