import React, { useEffect } from 'react';
import { Modal, Select, DatePicker, Input, Upload, Icon, AutoComplete } from 'antd';
import { useSelector, useDispatch } from 'react-redux';
import { fetchCategories } from './../../actions/categoriesAction';

import './style/form.less';

const { Option } = Select;
const { Dragger } = Upload;

export default function Form(props) {

    const state_form = useSelector(state => state.currentReducer);
    const state_cat = useSelector(state => state.categoriesReducer);
    const dispatch = useDispatch();

    const opt_cat = state_cat.categories.map(opt => <Option key={opt.id}>{opt.cat_name}</Option>)
    const opt_comp = state_cat.categories.map(opt => <AutoComplete.Option key={opt.id}>{opt.cat_name}</AutoComplete.Option>)
    const dragger_props = {
        accept: '.pdf',
        multiple: true,
        beforeUpload: file => {
            console.log(file);
            return false;
        },
    }

    useEffect(() => {
        dispatch(fetchCategories());
    }, []);
    return (
        <React.Fragment>
            <Modal
                title={state_form.formTitle}
                visible={state_form.isShowForm}
                okText="Save"
                onCancel={() => dispatch({ type: 'SHOW_FORM' })}
            >
                <div className="">
                    <div className="form-group">
                        <label className="control-label" htmlFor="currentCat">Category:</label>
                        <div className="">
                            <Select allowClear={true} placeholder="Select category" style={{ width: '100%' }}>
                                {opt_cat}
                            </Select>

                        </div>
                    </div>
                    <div className="form-group">
                        <label className="control-label" htmlFor="currentComp">Company name:</label>
                        <div className="">
                            <AutoComplete
                                allowClear={true}
                                placeholder="Type company name"
                                style={{ width: '100%' }}
                                filterOption={(inputValue, option) => option.props.children.toUpperCase().indexOf(inputValue.toUpperCase()) !== -1}
                            >
                                {opt_comp}
                            </AutoComplete >
                        </div>
                    </div>
                    <div className="form-group">
                        <label className="control-label" htmlFor="currentComp">Date signed:</label>
                        <div className="">
                            <DatePicker style={{ width: '100%' }} />
                        </div>
                    </div>
                    <div className="form-group current-validity">
                        <div >
                            <label className="control-label" htmlFor="currentComp">Valid from:</label>
                            <DatePicker />
                        </div>
                        <div>
                            <label className="control-label" htmlFor="currentComp">Valid to:</label>
                            <DatePicker />
                        </div>
                    </div>
                    <div className="form-group">
                        <label className="control-label" htmlFor="currentComp">Reminders:</label> <small>Add reminders prior to expiration date</small>
                        <div className="">
                            <Input placeholder="60 days" /> <small>(60 days default if empty)</small>
                        </div>
                    </div>
                    <div className="form-group">
                        <label className="control-label" htmlFor="currentComp">Notes</label>
                        <div className="">
                            <Input.TextArea placeholder="..." />
                        </div>
                    </div>
                    <div className="form-group">
                        <label className="control-label" htmlFor="currentComp">Attachment</label><small> (PDF only)</small>
                        <div className="">
                            <Dragger {...dragger_props}>
                                <p className="ant-upload-drag-icon">
                                    <Icon type="inbox" />
                                </p>
                                <p className="ant-upload-text">Click or drag file to this area to upload</p>
                                <p className="ant-upload-hint">Support for a single or bulk upload. </p>
                            </Dragger>,
                        </div>
                    </div>
                </div>
            </Modal>
        </React.Fragment>
    )
}