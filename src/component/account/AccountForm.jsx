import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { Modal, Select, Button, Input, Form, notification, Radio, Row, Col } from 'antd';
import { API_URL } from '../../helpers/constant';
import { isEmpty } from './../../helpers/utils';
import AccountServices from '../../services/accountServices';
import $ from 'jquery';

function AccountForm(props) {
    const { getFieldDecorator, setFieldsValue, resetFields } = props.form;
    const state_acc = useSelector(state => state.accountReducer);
    const dispatch = useDispatch();
    const [showInputPass, setShowInputPass] = useState(false);

    function submitData(data) {

        const { id, actionForm } = state_acc;
        // const user_id = jwt.get('user_id');
        const url = `${API_URL}/action_account.php`;
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
                        Modal.success({ content: 'Account Has Been Successfuly Added' });
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
                        Modal.success({ content: 'Account Has Been Successfuly Updated' });
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
            let res = AccountServices.getAccountById(stateForm);
            if (res.status === 'success') {
                //Set data to each corresponding field.
                let { username, password, firstname, lastname, status, user_role, email } = res.aaData[0];
                setFieldsValue({
                    username,
                    password,
                    firstname,
                    lastname,
                    status,
                    role: user_role,
                    email
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
    function toggleInputPass() {
        setShowInputPass((prev) =>
            !prev
        );
    }
    console.log('render account form');
    useEffect(() => {
        console.log('rerender effect');
        getData(state_acc);

        return () => {
            resetFields();
            setShowInputPass(false);
        }
    }, [state_acc.id]);
    return (
        <Modal
            title={state_acc.formTitle}
            visible={state_acc.isShowForm}
            okText="Save"
            onCancel={() => dispatch({ type: 'SHOW_FORM_ACCOUNT' })}
            footer={null}
        >
            <form onSubmit={onSubmit}>
                <div className="form-group">
                    <label className="control-label" htmlFor="username">Username:</label>
                    <Form.Item>
                        {
                            getFieldDecorator('username', {
                                rules: [{ required: true, message: 'this field is required' }],
                            })(<Input
                                name="username"
                                placeholder="Username" />
                            )}
                    </Form.Item>
                </div>
                <div className="form-group">
                    <label className="control-label" htmlFor="password">Password:</label>
                    {
                        state_acc.actionForm === 'add' || showInputPass ?
                            <Row type="flex" align="middle">
                                <Col span={state_acc.actionForm == 'add' ? 24 : 21}>
                                    <Form.Item>
                                        {
                                            getFieldDecorator('password', {
                                                rules: [{ required: true, message: 'this field is required' }],
                                            })(<Input.Password
                                                name="password"
                                                placeholder="Password" />
                                            )}
                                    </Form.Item>
                                </Col>
                                {
                                    showInputPass ?
                                        <Col span={3}>
                                            <Button title="cancel" style={{ width: '100%' }} type="danger" size="small" onClick={toggleInputPass}>X</Button>
                                        </Col>
                                        : null
                                }
                            </Row>
                            : <div ><a title="Edit Password" onClick={toggleInputPass}>•••••• change</a></div>
                    }
                </div>
                <Row type="flex" gutter={[10, 0]} className="form-group" >
                    <Col span={12}>
                        <label className="control-label" htmlFor="firstname">Firstname:</label>
                        <Form.Item>
                            {
                                getFieldDecorator('firstname', {
                                    rules: [{ required: true, message: 'this field is required' }],
                                })(<Input
                                    name="firstname"
                                    placeholder="Firstname" />
                                )}
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <label className="control-label" htmlFor="lastname">Lastname:</label>
                        <Form.Item>
                            {
                                getFieldDecorator('lastname', {
                                    rules: [{ required: true, message: 'this field is required' }],
                                })(<Input
                                    name="lastname"
                                    placeholder="Lastname" />
                                )}
                        </Form.Item>
                    </Col>
                </Row>
                <Row type="flex" gutter={[10, 0]} className="form-group">
                    <Col span={12}>
                        <label className="control-label" htmlFor="status">Status:</label>
                        <Form.Item>
                            {
                                getFieldDecorator('status', {
                                    rules: [{ required: true, message: 'this field is required' }],
                                })(<Radio.Group buttonStyle="solid">
                                    <Radio.Button value="ACTIVE">ACTIVE</Radio.Button>
                                    <Radio.Button value="INACTIVE">INACTIVE</Radio.Button>
                                </Radio.Group>
                                )}
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <label className="control-label" htmlFor="password">Role:</label>
                        <Form.Item>
                            {
                                getFieldDecorator('role', {
                                    rules: [{ required: true, message: 'this field is required' }],
                                })(<Select>
                                    <Select.Option value="USER">USER</Select.Option>
                                    <Select.Option value="ADMIN">ADMIN</Select.Option>
                                </Select>
                                )}
                        </Form.Item>
                    </Col>
                </Row>
                <div className="form-group">
                    <label className="control-label" htmlFor="email">Email:</label>
                    <Form.Item>
                        {
                            getFieldDecorator('email', {
                                rules: [{ required: true, message: 'this field is required' }],
                            })(<Input
                                name="email"
                                placeholder="Email" />
                            )}
                    </Form.Item>
                </div>
                <Row type="flex" gutter={[4, 0]} >
                    <Col md={{ span: 6, offset: 12 }} xs={12}>
                        <Button onClick={() => dispatch({ type: 'SHOW_FORM_ACCOUNT' })} style={{ width: '100%' }}>CLOSE</Button>
                    </Col>
                    <Col md={6} xs={12}>
                        <Button type="primary" style={{ width: '100%' }} htmlType="submit">SAVE</Button>
                    </Col>
                </Row>
            </form>


        </Modal>
    )
}


const WrappedAccount = Form.create()(AccountForm);
export default WrappedAccount;