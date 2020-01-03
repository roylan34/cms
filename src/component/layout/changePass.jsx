import React from 'react';
import { Button, Col, Form, Input, Modal, notification, Row } from 'antd';
import $ from 'jquery';
import { useDispatch, useSelector } from 'react-redux';
import { API_URL } from '../../helpers/constant';
import { isEmpty } from '../../helpers/utils';

function ChangePassForm(props) {
    const { getFieldDecorator, resetFields } = props.form;
    const state_pass = useSelector(state => state.changePassForm);
    const state_acc_id = useSelector(state => state.userDetailsReducer.id);
    const dispatch = useDispatch();

    function submitData(data) {

        if (!isEmpty(state_acc_id)) {

            const url = `${API_URL}/action_change_pass.php`;
            const param = data;
            param.id = state_acc_id;
            param.action = "change-pass";

            //Change password
            $.ajax({
                url: url,
                data: param,
                dataType: 'json',
                method: "POST",
                success: (res) => {
                    if (res.status === "success") {
                        Modal.success({ title: 'SUCCESS', content: 'Account Has Been Successfuly Added' });
                        resetFields();
                    }
                    else if (res.status === "failed") {
                        Modal.error({ title: 'ERROR', content: res.message });
                    }
                },
                error: (xhr, status) => {
                    notification.error({ message: 'Error', description: "Something went wrong! Can't save record" });
                }
            });

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

    function closeModal() {
        dispatch({ type: 'SHOW_FORM_CHANGEPASS' });
        resetFields();
    }

    return (
        <Modal
            title="Change Password"
            visible={state_pass.isShowForm}
            onCancel={closeModal}
            footer={null}
        >
            <form onSubmit={onSubmit}>
                <div className="form-group">
                    <label className="control-label" htmlFor="current_pass">Current Password:</label>
                    <Form.Item>
                        {
                            getFieldDecorator('current_pass', {
                                rules: [{ required: true, message: 'this field is required' }],
                            })(<Input.Password
                                name="current_pass"
                                placeholder="Current Password" />
                            )}
                    </Form.Item>
                </div>
                <div className="form-group">
                    <label className="control-label" htmlFor="new_pass">New Password:</label>
                    <Form.Item>
                        {
                            getFieldDecorator('new_pass', {
                                rules: [{ required: true, message: 'this field is required' }],
                            })(<Input.Password
                                name="new_pass"
                                placeholder="New Password" />
                            )}
                    </Form.Item>
                </div>
                <div className="form-group">
                    <label className="control-label" htmlFor="confirm_new_pass">Confirm New Password:</label>
                    <Form.Item>
                        {
                            getFieldDecorator('confirm_new_pass', {
                                rules: [{ required: true, message: 'this field is required' }],
                            })(<Input.Password
                                name="confirm_new_pass"
                                placeholder="New Password" />
                            )}
                    </Form.Item>
                </div>

                <Row type="flex" gutter={[4, 0]} >
                    <Col md={{ span: 6, offset: 12 }} xs={12}>
                        <Button onClick={closeModal} style={{ width: '100%' }}>CLOSE</Button>
                    </Col>
                    <Col md={6} xs={12}>
                        <Button type="primary" style={{ width: '100%' }} htmlType="submit">SAVE</Button>
                    </Col>
                </Row>
            </form>


        </Modal>
    )
}


const WrappedChangePass = Form.create()(ChangePassForm);
export default WrappedChangePass;