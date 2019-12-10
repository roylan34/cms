import React, { useEffect, useState } from 'react';
import { Modal, Select, DatePicker, Input, Upload, Icon, Form, notification, Spin, Button, Row, Col } from 'antd';
import { useSelector, useDispatch } from 'react-redux';
import _debounce from 'lodash.debounce';
import { fetchComp, fetchCompById } from '../../actions/drpAction';
import { isEmpty, momentObjToString, isEmptyStr } from '../../helpers/utils';
import { API_URL } from '../../helpers/constant';
import { SelectCategory } from '../../helpers/dropdown';
import CurrentServices from '../../services/currentServices.js';
import File from '../../helpers/fileUpload';
import './current-style.less';
import moment from 'moment';

const { Option } = Select;

function FormCurrent(props) {
    const { getFieldDecorator, setFieldsValue, resetFields } = props.form;

    const state_form = useSelector(state => state.currentReducer);
    const state_drp = useSelector(state => state.drpReducer);
    const dispatch = useDispatch();
    const [stateAttach, setStateAttach] = useState({ dir: '', dir_ren: '', remove_file: [] });

    const opt_comp = state_drp.comp.data.map(opt => <Option key={`id-${opt.id}`} value={opt.id}>{opt.company_name}</Option>)
    const delayedSearchComp = _debounce((e) => onSearchCompany(e), 500);

    function onFilterSearchComp(inputValue, option) {//non-case-sensitive when searching
        return (option.props.children.toUpperCase().indexOf(inputValue.toUpperCase()) !== -1);
    }
    function onSearchCompany(input) {
        dispatch(fetchComp(input));
    }

    function submitData(data, reset, state) {
        // console.log(props.dtInstance.current.ajax.reload(false,null));
        const { comp, category, valid_from, valid_to, days_to_reminds, notes, attachment } = data;
        const { id, actionForm } = state_form;
        // const user_id = jwt.get('user_id');
        const param = { action: actionForm, comp, category, valid_from: momentObjToString(valid_from), valid_to: momentObjToString(valid_to), days_to_reminds, notes: isEmptyStr(notes) };
        const url = `${API_URL}/action_current.php`;

        if (actionForm == 'add') {
            //Add form
            File.upload({
                url: url,
                attachment,
                data: param,
                cbSuccess: (res) => {
                    if (res.status === "success") {
                        Modal.success({ content: 'Contract Has Been Successfuly Added' });
                        props.refreshTable();
                    }
                    if (res.attachment === "invalid") {
                        notification.error({ message: 'Error', description: "Something went wrong in your Attachment. Please check!" });
                    }
                },
                cbError: (xhr, status) => {
                    if (status === "abort")
                        notification.error({ message: 'Error', description: "Please don't close the form when you saving." });
                },
                cbComplete: () => {
                    reset();
                }
            })

        }
        else if (actionForm === 'edit') {
            //Update form
            param.id = id;
            param.attachment_dir = state.dir;
            param.attachment_renew_dir = state.dir_ren;
            param.remove_files = JSON.stringify(state.remove_file);
            File.upload({
                url: url,
                attachment,
                data: param,
                cbSuccess: (res) => {
                    if (res.status === "success") {
                        Modal.success({ content: 'Contract Has Been Successfuly Updated' });
                        if (res.attachment === "ok" && res.hasOwnProperty('attachment_files')) {
                            setFieldsValue({ attachment: res.attachment_files });
                        }
                        props.refreshTable();
                    }
                    if (res.attachment === "invalid") {
                        notification.error({ message: 'Error', description: "Something went wrong in your Attachment. Please check!" });
                    }
                },
                cbError: (xhr, status) => {
                    if (status === "abort")
                        notification.error({ message: 'Error', description: "Please don't close the form when you saving." });
                },
                cbComplete: () => {
                    reset((prev) => ({ ...prev, remove_file: [] }));
                }
            })
        }
        else if (actionForm === 'renew') {
            param.id = id;
            param.attachment_dir = state.dir;
            File.upload({
                url: url,
                attachment,
                data: param,
                cbSuccess: (res) => {
                    if (res.status === "success") {
                        Modal.success({
                            content: 'Contract Has Been Successfuly Renew',
                            onOk() {
                                dispatch({ type: 'HIDE_CURRENT_FORM' });
                            }
                        });
                        props.refreshTable();
                    }
                    if (res.attachment === "invalid") {
                        notification.error({ message: 'Error', description: "Something went wrong in your Attachment. Please check!" });
                    }
                },
                cbError: (xhr, status) => {
                    if (status === "abort")
                        notification.error({ message: 'Error', description: "Please don't close the form when you saving." });
                }
            })
        }
    }
    function getData(stateForm) {
        //fetch data
        if (!isEmpty(stateForm.id)) {
            let res = CurrentServices.getCurrentById(stateForm);
            if (res.status === 'success') {

                // set data to each corresponding field.
                let { category, sap_company_id, valid_from, valid_to, days_to_reminds, notes, attachment, ren_attachment, attachment_files } = res.aaData[0];

                dispatch(fetchCompById(sap_company_id)); //Set default Select.option
                setStateAttach((prevState) => ({ ...prevState, dir: attachment, dir_ren: ren_attachment }));

                const setValue = { comp: sap_company_id, category };
                if (stateForm.actionForm === "edit") {
                    setValue.valid_from = moment(valid_from, 'YYYY-MM-DD');
                    setValue.valid_to = moment(valid_to, 'YYYY-MM-DD');
                    setValue.days_to_reminds = days_to_reminds;
                    setValue.notes = notes;
                    setValue.attachment = attachment_files;
                }
                setFieldsValue(setValue);
            }
        }
    }
    function onSubmit(e) {
        e.preventDefault();
        props.form.validateFields((err, val) => {
            if (!err) {
                submitData(val, (!isEmpty(state_form.id) ? setStateAttach : resetFields), stateAttach);
            }
        });
    }
    function normFile(e) {
        // console.log("Upload event:", e);
        if (Array.isArray(e)) {
            return e;
        }
        return e && e.fileList;
    };
    function resetState() {
        setStateAttach({ dir: '', dir_ren: '', remove_file: [] });
    }
    function handleChangeComp(value) {
        dispatch({ type: 'HALT_FETCHING_COMP' });
    }
    const dragger_props = {
        name: 'files',
        accept: '.pdf',
        multiple: true,
        beforeUpload(file, fileList) {
            if (file.type !== 'application/pdf') {
                file.flag = 1;
                fileList.length = 0;
            }
            const isLt2M = file.size / 1024 / 1024 < 2;
            if (!isLt2M) {
                file.flag = 2;
                fileList.length = 0;
            }
            return false;
        },
        onChange(info) {
            if (info.file.flag == 1) {
                notification.error({ message: 'Error', description: `${info.file.name} document format error` });
                return;
            }
            else if (info.file.flag == 2) {
                notification.error({ message: 'Error', description: `${info.file.name} PDF size more than limit(2M)` });
                return;
            }

        },
        onRemove(file) {
            if (!isEmpty(state_form.id) && file.hasOwnProperty('url')) {//Only uploaded file will set to state for removing.
                setStateAttach((prevState) => ({
                    ...prevState,
                    remove_file: [...prevState.remove_file, file.name]
                }))
            }
        }
    };
    function resetForm() {
        dispatch({ type: 'HIDE_CURRENT_FORM' });
        resetState();
        resetFields();
    }
    console.log('render formsss');
    useEffect(() => {
        getData(state_form);

    }, [state_form.isShowForm]);


    return (
        <React.Fragment>
            <Modal
                title={state_form.formTitle}
                visible={state_form.isShowForm}
                okText="Save"
                onCancel={resetForm}
                footer={null}
            >
                <form onSubmit={onSubmit}>
                    <div className="">
                        <div className="form-group">
                            <label className="control-label" htmlFor="currentCat">Category:</label>
                            <Form.Item>
                                {
                                    getFieldDecorator('category', {
                                        rules: [{ required: true, message: 'this field is required' }],
                                    })(<SelectCategory
                                        name="category"
                                        placeholder="Select category" />
                                    )}
                            </Form.Item>
                        </div>
                        <div className="form-group">
                            <label className="control-label" htmlFor="currentComp">Company name: </label><small> (SAP registered)</small>
                            <Form.Item>
                                {
                                    getFieldDecorator('comp', {
                                        rules: [{ required: true, message: 'this field is required' }]
                                    })(<Select
                                        name="comp"
                                        disabled={state_form.actionForm === "renew" ? true : false}
                                        allowClear={true}
                                        placeholder="Type company name"
                                        style={{ width: '100%' }}
                                        filterOption={onFilterSearchComp}
                                        onSearch={delayedSearchComp}
                                        showSearch
                                        onChange={handleChangeComp}
                                        notFoundContent={state_drp.comp.isFetching ? <div><Spin size="small" /> Loading...</div> : null}
                                    >
                                        {opt_comp}
                                    </Select >)
                                }
                            </Form.Item>
                        </div>

                        <div className="form-group current-validity">
                            <div>
                                <label className="control-label" htmlFor="">Valid from:</label>
                                <Form.Item>
                                    {
                                        getFieldDecorator('valid_from', {
                                            rules: [{ required: true, message: 'this field is required' }]
                                        })(
                                            <DatePicker />
                                        )
                                    }
                                </Form.Item>
                            </div>
                            <div>
                                <label className="control-label" htmlFor="">Valid to:</label>
                                <Form.Item>
                                    {
                                        getFieldDecorator('valid_to', {
                                            rules: [{ required: true, message: 'this field is required' }]
                                        })(
                                            <DatePicker />
                                        )
                                    }
                                </Form.Item>
                            </div>
                        </div>
                        <div className="form-group">
                            <label className="control-label" htmlFor="">Reminders:</label> <small>Add reminders prior to expiration date</small>
                            <Form.Item>
                                {
                                    getFieldDecorator('days_to_reminds', {
                                        rules: [{ required: true, message: 'this field is required' }],
                                        initialValue: 1
                                    })(
                                        <Input placeholder="# of days" />
                                    )
                                }
                            </Form.Item>
                        </div>
                        <div className="form-group">
                            <label className="control-label" htmlFor="">Notes</label>
                            <Form.Item>
                                {
                                    getFieldDecorator('notes')(<Input.TextArea placeholder="..." name="notes" />)
                                }
                            </Form.Item>
                        </div>
                        <div className="form-group">
                            <label className="control-label" htmlFor="">Attachment</label><small> (PDF only; max file size 2MB)</small>
                            <Form.Item>
                                {
                                    getFieldDecorator('attachment', {
                                        rules: [{ required: true, message: 'this field is required' }],
                                        valuePropName: "fileList",
                                        getValueFromEvent: normFile
                                    })(<Upload.Dragger {...dragger_props}>
                                        <p className="ant-upload-drag-icon">
                                            <Icon type="inbox" />
                                        </p>
                                        <p className="ant-upload-text">Click or drag file to this area to upload</p>
                                        <p className="ant-upload-hint">Support for a single or bulk upload. </p>
                                    </Upload.Dragger>)
                                }
                            </Form.Item>
                        </div>
                    </div>
                    <Row type="flex" gutter={[4, 0]} >
                        <Col md={{ span: 6, offset: 12 }} xs={12}>
                            <Button style={{ width: '100%' }} onClick={resetForm}>CLOSE</Button>
                        </Col>
                        <Col md={6} xs={12}>
                            <Button type="primary" style={{ width: '100%' }} htmlType="submit">SAVE</Button>
                        </Col>
                    </Row>
                </form>
            </Modal>
        </React.Fragment>
    )
}

const WrappedComp = Form.create()(FormCurrent);
export default WrappedComp;