import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { Modal, Select, Button, Input, Form, notification, Radio, Row, Col } from 'antd';
import { API_URL } from '../../helpers/constant';
import { isEmpty } from './../../helpers/utils';
import CategoryServices from '../../services/categoryServices';
import $ from 'jquery';



function CategoryForm(props) {
    const { getFieldDecorator, setFieldsValue, resetFields } = props.form;
    const state_cat = useSelector(state => state.categoryReducer);
    const dispatch = useDispatch();

    function submitData(data) {
        const { id, actionForm } = state_cat;
        // const user_id = jwt.get('user_id');
        const url = `${API_URL}/action_category.php`;
        const param = data;
        param.action = actionForm;

        if (actionForm == 'add') {
            //Add form
            $.ajax({
                url: url,
                data: param,
                dataType: 'json',
                method: "POST",
                success: (res) => {
                    if (res.status === "success") {
                        Modal.success({ content: 'Category Has Been Successfuly Added' });
                        props.refreshTable();
                        resetFields();
                    }
                },
                error: (xhr, status) => {
                    notification.error({ message: 'Error', description: "Something went wrong! Can't save record" });
                }
            });

        }
        else if (actionForm === 'edit') {
            //Update form
            param.id = id;
            $.ajax({
                url: url,
                data: param,
                dataType: 'json',
                method: "POST",
                success: (res) => {
                    if (res.status === "success") {
                        Modal.success({ content: 'Category Has Been Successfuly Updated' });
                        props.refreshTable();
                    }
                },
                error: (xhr, status) => {
                    notification.error({ message: 'Error', description: "Something went wrong! Can't save record" });
                }
            });
        }
    }

    function getData(stateForm) {
        //Fetch data
        if (!isEmpty(stateForm.id)) {
            let res = CategoryServices.getCategoryById(stateForm);
            if (res.status === 'success') {
                //Set data to each corresponding field.
                let { cat_name, status } = res.aaData[0];
                setFieldsValue({
                    category: cat_name,
                    status
                });
            }
        }
    }

    function onSubmit(e) {
        e.preventDefault();
        props.form.validateFields((err, val) => {
            if (!err) {
                submitData(val);
            }
        });
    }
    console.log('rerender comp');
    useEffect(() => {
        console.log('rerender effect');
        getData(state_cat);

        return () => {
            resetFields();
        }
    }, [state_cat.id]);
    return (
        <Modal
            title={state_cat.formTitle}
            visible={state_cat.isShowForm}
            okText="Save"
            onCancel={() => dispatch({ type: 'SHOW_FORM_CATEGORY' })}
            footer={null}
        >
            <form onSubmit={onSubmit}>
                <div style={{ marginBottom: 30 }}>
                    <Row type="flex" gutter={[10, 0]}>
                        <Col span={12}>
                            <label className="control-label" htmlFor="username">Category name:</label>
                            <Form.Item>
                                {
                                    getFieldDecorator('category', {
                                        rules: [{ required: true, message: 'this field is required' }],
                                    })(<Input
                                        name="category"
                                        placeholder="Category"
                                    />
                                    )}
                            </Form.Item>
                        </Col>
                        <Col span={12}>
                            <label className="control-label" htmlFor="status">Status:</label>
                            <Form.Item>
                                {
                                    getFieldDecorator('status', {
                                        rules: [{ required: true, message: 'this field is required' }],
                                        initialValue: 'ACTIVE'
                                    })(<Radio.Group buttonStyle="solid">
                                        <Radio.Button value="ACTIVE">ACTIVE</Radio.Button>
                                        <Radio.Button value="INACTIVE">INACTIVE</Radio.Button>
                                    </Radio.Group>
                                    )}
                            </Form.Item>
                        </Col>
                    </Row>
                </div>
                <Row type="flex" gutter={[4, 0]} >
                    <Col md={{ span: 6, offset: 12 }} xs={12}>
                        <Button onClick={() => dispatch({ type: 'SHOW_FORM_CATEGORY' })} style={{ width: '100%' }}>CLOSE</Button>
                    </Col>
                    <Col md={6} xs={12}>
                        <Button type="primary" style={{ width: '100%' }} htmlType="submit">SAVE</Button>
                    </Col>
                </Row>
            </form>
        </Modal>
    )
}

const WrappedCategory = Form.create()(CategoryForm);
export default WrappedCategory;